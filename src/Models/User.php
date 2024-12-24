<?php

namespace MVC\Models;
use PDO;
class User extends BaseModel {
    // Add constants for user types
    const ADMIN_TYPE = 1;
    const TEACHER_TYPE = 2;
    const STUDENT_TYPE = 3;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo, 'users');
    }

    public function validateUser($username, $password) {
        try {
            $conditions = [
                ['field' => 'username', 'operator' => '=', 'value' => $username]
            ];
            
            $users = $this->get($conditions);
            
            if (!empty($users)) {
                $user = $users[0];
                if (password_verify($password, $user['password'])) {
                    return $user;
                }
            }
            
            return false;
        } catch (\PDOException $e) {
            error_log("Error in validateUser: " . $e->getMessage());
            return false;
        }
    }

    public function getAll()
    {
        return $this->get([], null, null, 'name ASC');
    }

    public function getById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }

    public function updateTeacher($id, $username, $email, $password)
    {
        return $this->update(
            [
                'username' => $username,
                'email' => $email,
                'password' => $password
            ],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }
    public function editUser($id, $username,$email)
    {
        return $this->update(
            [
                'username' => $username,
                'email' => $email,

            ],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteTeacher($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }
    public function isEmailExists(string $email): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return (bool) $stmt->fetchColumn();
    }

    public function isPhoneExists(string $phone): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        return (bool) $stmt->fetchColumn();
    }
    public function find($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function incrementOtpAttempts($userId) {
        $sql = "UPDATE users SET otp_attempts = otp_attempts + 1 WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([$userId]);
    }
    public function getLastOtpTime($phone) {
        $sql = "SELECT last_otp_sent FROM users WHERE phone = ? ORDER BY last_otp_sent DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$phone]);
        return $stmt->fetchColumn();
    }
}