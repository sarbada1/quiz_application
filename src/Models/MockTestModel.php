<?php

namespace MVC\Models;

use Exception;
use PDO;
use PDOException;

class MockTestModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'programmes_mock_test');
    }

    public function getAll()
    {
        $sql = "SELECT * FROM programmes_mock_test";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching mock tests: " . $e->getMessage());
        }
    }

    public function getByProgramId($id)
    {
        return $this->get([['field' => 'program_id', 'operator' => '=', 'value' => $id]]);
    }
    
    public function getById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }
    public function getBySlug($slug)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM programmes_mock_test WHERE slug = ?");
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in getBySlug: " . $e->getMessage());
            return null;
        }
    }


    public function createMockTest($program_id, $name, $time,$slug,$no_of_student,$exam_time,$date)
    {
        return $this->insert([
            'program_id' => $program_id,
            'name' => $name,
            'slug' => $slug,
            'time' => $time,
            'no_of_student' => $no_of_student,
            'exam_time' => $exam_time,
            'date' => $date,
        ]);
    }

    public function updateMockTest($id, $name, $time,$slug,$no_of_student,$exam_time,$date)
    {
        return $this->update(
            [
                'name' => $name,
                'slug' => $slug,
                'time' => $time,
                'no_of_student' => $no_of_student,
                'exam_time' => $exam_time,
                'date' => $date,
            ],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteMockTest($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }
    public function saveProgress($data)
{
    $sql = "INSERT INTO saved_mock_test_progress 
            (user_id, mock_test_id, question_id, selected_answer_id, remaining_time) 
            VALUES (:user_id, :mock_test_id, :question_id, :selected_answer_id, :remaining_time)";
            
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($data);
}

public function clearSavedProgress($userId, $mockTestId)
{
    $sql = "DELETE FROM saved_mock_test_progress 
            WHERE user_id = :user_id AND mock_test_id = :mock_test_id";
            
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        'user_id' => $userId,
        'mock_test_id' => $mockTestId
    ]);
}

public function getProgress($userId, $mockTestId)
{
    $sql = "SELECT * FROM saved_mock_test_progress 
            WHERE user_id = :user_id AND mock_test_id = :mock_test_id";
            
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'user_id' => $userId,
        'mock_test_id' => $mockTestId
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getAllMockTests($programId)
{
    $sql = "SELECT pmt.*, 
                   (pmt.no_of_student - COUNT(mr.id)) as available_seats
            FROM programmes_mock_test pmt
            LEFT JOIN mocktest_registrations mr ON pmt.id = mr.mocktest_id
            WHERE pmt.program_id = :program_id
            GROUP BY pmt.id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':program_id' => $programId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getAvailableSeats($mocktestId)
{
    $sql = "SELECT no_of_student - COUNT(mr.id) as available_seats
            FROM programmes_mock_test pmt
            LEFT JOIN mocktest_registrations mr ON pmt.id = mr.mocktest_id
            WHERE pmt.id = :mocktest_id
            GROUP BY pmt.id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':mocktest_id' => $mocktestId]);
    return $stmt->fetchColumn();
}
public function isUserRegistered($userId, $mocktestId)
{
    $sql = "SELECT COUNT(*) FROM mocktest_registrations
            WHERE user_id = :user_id AND mocktest_id = :mocktest_id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':user_id' => $userId, ':mocktest_id' => $mocktestId]);
    return $stmt->fetchColumn() > 0;
}

public function registerUser($userId, $mocktestId)
{
    $sql = "INSERT INTO mocktest_registrations (user_id, mocktest_id)
            VALUES (:user_id, :mocktest_id)";

    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([':user_id' => $userId, ':mocktest_id' => $mocktestId]);
}
}