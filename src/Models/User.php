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

}
