<?php
// src/WebSocketServer.php
namespace MVC;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class WebSocketServer implements MessageComponentInterface {
    protected $clients;
    protected $activeExams = []; // Stores exam_id => [start_time, end_time, participants]
    protected $examParticipants = []; // Stores exam_id => [user_id => connection]
    protected $userExams = []; // Stores connection => [user_id, exam_id, submitted]

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }
    
    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        
        if (!isset($data['type'])) {
            return;
        }
        
        switch($data['type']) {
            case 'join_exam':
                $this->handleJoinExam($from, $data);
                break;
            case 'submit_exam':
                $this->handleSubmitExam($from, $data);
                break;
            case 'admin_start_exam':
                $this->handleStartExam($from, $data);
                break;
            case 'admin_end_exam':
                $this->handleEndExam($from, $data);
                break;
            case 'heartbeat':
                // Just acknowledge the heartbeat
                $from->send(json_encode(['type' => 'heartbeat_ack']));
                break;
        }
    }

    private function handleJoinExam($conn, $data) {
        $examId = $data['exam_id'];
        $userId = $data['user_id'];
        
        // Check if user already submitted this exam
        if ($this->hasUserSubmittedExam($userId, $examId)) {
            $conn->send(json_encode([
                'type' => 'error',
                'message' => 'You have already completed this exam.'
            ]));
            return;
        }
        
        // Track this user's connection
        $this->userExams[$conn->resourceId] = [
            'user_id' => $userId,
            'exam_id' => $examId,
            'submitted' => false
        ];
        
        if (!isset($this->examParticipants[$examId])) {
            $this->examParticipants[$examId] = [];
        }
        
        $this->examParticipants[$examId][$userId] = $conn;
        
        // Check if exam is already in progress
        if (isset($this->activeExams[$examId])) {
            $timeRemaining = max(0, $this->activeExams[$examId]['end_time'] - time());
            
            $conn->send(json_encode([
                'type' => 'exam_status',
                'status' => 'in_progress',
                'time_remaining' => $timeRemaining,
                'total_participants' => count($this->examParticipants[$examId])
            ]));
        } else {
            $conn->send(json_encode([
                'type' => 'exam_status',
                'status' => 'waiting',
                'message' => 'Waiting for the exam to start...',
                'total_participants' => count($this->examParticipants[$examId])
            ]));
        }
    }
    
    private function handleSubmitExam($conn, $data) {
        if (!isset($this->userExams[$conn->resourceId])) {
            return;
        }
        
        $userInfo = $this->userExams[$conn->resourceId];
        $examId = $userInfo['exam_id'];
        $userId = $userInfo['user_id'];
        
        // Mark as submitted
        $this->userExams[$conn->resourceId]['submitted'] = true;
        
        $conn->send(json_encode([
            'type' => 'exam_submitted',
            'message' => 'Your exam has been submitted successfully.'
        ]));
        
        // Notify admin about submission (optional)
        $this->broadcastToAdmin($examId, [
            'type' => 'student_submitted',
            'user_id' => $userId,
            'remaining_students' => $this->getActiveParticipantCount($examId)
        ]);
    }
    
    private function handleStartExam($conn, $data) {
        // Verify this is an admin (you'll need to implement proper auth)
        if (!isset($data['exam_id']) || !isset($data['duration'])) {
            return;
        }
        
        $examId = $data['exam_id'];
        $duration = $data['duration']; // in seconds
        
        $startTime = time();
        $endTime = $startTime + $duration;
        
        $this->activeExams[$examId] = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration' => $duration
        ];
        
        // Notify all participants
        if (isset($this->examParticipants[$examId])) {
            foreach ($this->examParticipants[$examId] as $participant) {
                $participant->send(json_encode([
                    'type' => 'exam_started',
                    'time_remaining' => $duration,
                    'end_time' => $endTime
                ]));
            }
        }
        
        // Schedule exam end
        $this->scheduleExamEnd($examId, $duration);
    }
    
    private function handleEndExam($conn, $data) {
        $examId = $data['exam_id'];
        $this->endExam($examId);
    }
    
    private function endExam($examId) {
        if (!isset($this->activeExams[$examId])) {
            return;
        }
        
        // Notify all participants
        if (isset($this->examParticipants[$examId])) {
            foreach ($this->examParticipants[$examId] as $participant) {
                $participant->send(json_encode([
                    'type' => 'exam_ended',
                    'message' => 'The exam has ended.'
                ]));
            }
        }
        
        // Clean up
        unset($this->activeExams[$examId]);
    }
    
    private function scheduleExamEnd($examId, $duration) {
        // In a real implementation, you might use a timer or task queue
        // For simplicity, we're just logging that we would schedule it
        echo "Scheduled exam $examId to end in $duration seconds\n";
        
        // In production, you'd use something like:
        // $loop->addTimer($duration, function() use ($examId) {
        //     $this->endExam($examId);
        // });
    }
    
    private function hasUserSubmittedExam($userId, $examId) {
        foreach ($this->userExams as $info) {
            if ($info['user_id'] == $userId && $info['exam_id'] == $examId && $info['submitted']) {
                return true;
            }
        }
        return false;
    }
    
    private function getActiveParticipantCount($examId) {
        $count = 0;
        if (isset($this->examParticipants[$examId])) {
            foreach ($this->examParticipants[$examId] as $userId => $conn) {
                $resourceId = $conn->resourceId;
                if (isset($this->userExams[$resourceId]) && !$this->userExams[$resourceId]['submitted']) {
                    $count++;
                }
            }
        }
        return $count;
    }
    
    private function broadcastToAdmin($examId, $message) {
        // In a real implementation, you'd have admin connections to send to
        echo "Admin message for exam $examId: " . json_encode($message) . "\n";
    }

    public function onClose(ConnectionInterface $conn) {
        // Clean up user tracking when they disconnect
        if (isset($this->userExams[$conn->resourceId])) {
            $userInfo = $this->userExams[$conn->resourceId];
            $examId = $userInfo['exam_id'];
            $userId = $userInfo['user_id'];
            
            if (isset($this->examParticipants[$examId][$userId])) {
                unset($this->examParticipants[$examId][$userId]);
            }
            
            unset($this->userExams[$conn->resourceId]);
        }
        
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}



require dirname(__DIR__) . '/vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WebSocketServer()
        )
    ),
    8080
);

$server->run();