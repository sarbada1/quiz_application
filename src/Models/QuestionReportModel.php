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

    public function getAllReports()
    {
        $sql = "SELECT r.*, q.question_text, u.username as reporter_name 
                FROM question_reports r
                JOIN questions q ON r.question_id = q.id
                JOIN users u ON r.user_id = u.id
                ORDER BY r.created_at DESC";
        
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
}