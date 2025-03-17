<?php

namespace MVC\Models;

use PDO;

class ActivityLogModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function log($userId, $actionType, $description, $icon) {
        $sql = "INSERT INTO activity_logs (user_id, action_type, description, icon) 
                VALUES (:user_id, :action_type, :description, :icon)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'action_type' => $actionType,
            'description' => $description,
            'icon' => $icon
        ]);
    }

    public function getRecentActivities($limit = 5) {
        $sql = "SELECT al.*, u.username 
                FROM activity_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                ORDER BY al.created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(function($activity) {
            $timeAgo = $this->timeAgo($activity['created_at']);
            return [
                'icon' => $activity['icon'],
                'description' => $this->formatDescription($activity),
                'time' => $timeAgo
            ];
        }, $activities);
    }

    private function timeAgo($timestamp) {
        try {
            date_default_timezone_set('Asia/Kathmandu');
            
            $datetime = new \DateTime($timestamp);
            $now = new \DateTime();
            $diff = $now->getTimestamp() - $datetime->getTimestamp();
            
            if ($diff < 60) {
                return 'Just now';
            }
            
            if ($diff < 3600) {
                $minutes = floor($diff / 60);
                return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
            }
            
            if ($diff < 86400) {
                $hours = floor($diff / 3600);
                return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
            }
            
            if ($diff < 604800) {
                $days = floor($diff / 86400);
                return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
            }
            
            if ($diff < 2630880) {
                $weeks = floor($diff / 604800);
                return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
            }
            
            if ($diff < 31570560) {
                $months = floor($diff / 2630880);
                return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
            }
            
            $years = floor($diff / 31570560);
            return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
            
        } catch (\Exception $e) {
            error_log("Error calculating time ago: " . $e->getMessage());
            return 'some time ago';
        }
    }
    private function formatDescription($activity) {
        switch($activity['action_type']) {
            case 'test_attempt':
                return "{$activity['username']} attempted a mock test";
            case 'student_register':
                return "New student {$activity['username']} registered";
            case 'question_report':
                return "A question was reported by {$activity['username']}";
            default:
                return $activity['description'];
        }
    }
}