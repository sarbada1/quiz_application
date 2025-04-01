<?php

namespace MVC\Models;

use PDO;

class ExamSessionModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'exam_sessions');
    }
    
    public function createSession($examId, $startTime, $endTime)
    {
        $sql = "INSERT INTO exam_sessions (exam_id, start_time, end_time, status) 
                VALUES (:exam_id, :start_time, :end_time, 'pending')";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':exam_id' => $examId,
            ':start_time' => $startTime,
            ':end_time' => $endTime
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    public function getActiveSession($examId)
    {
        $sql = "SELECT * FROM exam_sessions 
                WHERE exam_id = :exam_id AND 
                (status = 'pending' OR status = 'in_progress')
                ORDER BY created_at DESC LIMIT 1";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':exam_id' => $examId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateSessionStatus($sessionId, $status)
    {
        $sql = "UPDATE exam_sessions SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $sessionId,
            ':status' => $status
        ]);
    }
    
    public function getExamStatus($examId)
    {
        $now = date('Y-m-d H:i:s');
        
        $sql = "SELECT es.*, 
                CASE 
                    WHEN q.status = 'draft' THEN 'draft'
                    WHEN es.start_time > :now THEN 'waiting'
                    WHEN es.end_time < :now THEN 'ended'
                    ELSE 'in_progress'
                END AS current_status,
                q.status AS quiz_status
                FROM exam_sessions es
                JOIN quizzes q ON es.exam_id = q.id
                WHERE es.exam_id = :exam_id
                ORDER BY es.created_at DESC LIMIT 1";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':exam_id' => $examId,
            ':now' => $now
        ]);
        
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$session) {
            // Check if quiz exists and get its status
            $sql = "SELECT status FROM quizzes WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $examId]);
            $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$quiz) {
                return ['status' => 'not_found'];
            }
            
            if ($quiz['status'] === 'draft') {
                return ['status' => 'draft'];
            }
            
            return ['status' => 'not_scheduled'];
        }
        
        // Auto-update status based on time
        if ($session['current_status'] !== $session['status']) {
            $this->updateSessionStatus($session['id'], $session['current_status']);
            $session['status'] = $session['current_status'];
        }
        
        return $session;
    }

    public function getLatestSessionForExam($examId)
{
    $sql = "SELECT * FROM exam_sessions 
            WHERE exam_id = :exam_id
            ORDER BY created_at DESC LIMIT 1";
            
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':exam_id' => $examId]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function updateSession($sessionId, $startTime, $endTime)
{
    $sql = "UPDATE exam_sessions 
            SET start_time = :start_time, end_time = :end_time 
            WHERE id = :id";
            
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        ':id' => $sessionId,
        ':start_time' => $startTime,
        ':end_time' => $endTime
    ]);
}
}