<?php
namespace MVC\Models;

use PDO;

class QuestionReportModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'question_reports');
    }

    public function createReport($data)
    {
        $sql = "INSERT INTO question_reports (question_id, user_id, reason, description, status) 
                VALUES (:question_id, :user_id, :reason, :description, 'pending')";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'question_id' => $data['question_id'],
            'user_id' => $data['user_id'],
            'reason' => $data['reason'],
            'description' => $data['description']
        ]);
    }
    public function createPreviousReport($data)
    {
        $sql = "INSERT INTO previous_question_reports (question_id, user_id, reason, description, status) 
                VALUES (:question_id, :user_id, :reason, :description, 'pending')";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'question_id' => $data['question_id'],
            'user_id' => $data['user_id'],
            'reason' => $data['reason'],
            'description' => $data['description']
        ]);
    }

    public function getAllReports()
    {
        $sql = "
            SELECT r.*, q.question_text, u.username as reporter_name, 'normal' as source
            FROM question_reports r
            JOIN questions q ON r.question_id = q.id
            JOIN users u ON r.user_id = u.id
            UNION
            SELECT pr.*, pq.question_text, u.username as reporter_name, 'previous_year' as source
            FROM previous_question_reports pr
            JOIN previous_year_questions pq ON pr.question_id = pq.id
            JOIN users u ON pr.user_id = u.id
            ORDER BY 
                CASE 
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'reviewed' THEN 2
                    ELSE 3
                END, 
                created_at DESC
        ";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($reportId, $status)
    {
        $sql = "UPDATE question_reports SET status = :status, updated_at = NOW() 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $reportId,
            'status' => $status
        ]);
    }

    public function getCount()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM question_reports");
        return $stmt->fetchColumn();
    }
    public function getUserReports($userId)
    {
        $sql = "SELECT qr.*, q.question_text 
                FROM question_reports qr
                JOIN questions q ON qr.question_id = q.id
                WHERE qr.user_id = :user_id
                ORDER BY qr.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUnreadReportsCount($userId)
    {
        $sql = "SELECT COUNT(*) as unread_count 
                FROM question_reports 
                WHERE user_id = :user_id AND status = 'pending'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
    }
}