<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\User;
use PDO;

class TeacherController extends Controller
{
    protected $pdo;
    private $userModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }
    public function showTeacherForm()
    {
        $content = $this->render('admin/teacher/addTeacher');
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function getDashboardCounts() {
        $sql = "SELECT 
            (SELECT COUNT(*) FROM users WHERE usertype_id = 3) as student_count,
            (SELECT COUNT(*) FROM users WHERE usertype_id = 2) as teacher_count,
            (SELECT COUNT(*) FROM questions) as question_count,
            (SELECT COUNT(*) FROM question_reports) as report_count";
            
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function addTeacher()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['cpassword'] ?? '';

            // Basic validation
            if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
                // Handle error, e.g., display an error message
                echo "All fields are required.";
                return;
            }

            if ($password !== $confirmPassword) {
                // Handle error, e.g., display an error message
                echo "Passwords do not match.";
                return;
            }

            // Password hashing
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare SQL statement
            $user = new User($this->pdo);
            $data = [
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'usertype_id' => 2
            ];

            $result = $user->insert($data);

            if ($result) {
                $_SESSION['message'] = "Teacher added successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/teacher/add');
            } else {
                // Handle error, e.g., display an error message or redirect with error message
                return $this->render('admin/teacher/addTeacher', ['error' => 'Error adding teacher.']);
            }
        }
    }

    public function listTeacher()
    {
        $userModel = new User($this->pdo);
        $teachers = $userModel->get([['field' => 'usertype_id', 'operator' => '=', 'value' => 2]]);
        $content = $this->render('admin/teacher/viewTeacher', ['teachers' => $teachers]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function listStudent()
    {
        $userModel = new User($this->pdo);
        $teachers = $userModel->get([['field' => 'usertype_id', 'operator' => '=', 'value' => 3]]);
        $content = $this->render('admin/student/view', ['teachers' => $teachers]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function edit($id)
    {
        $teacher = $this->userModel->getById($id);
        if (!$teacher) {
            echo "Teacher not found.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['cpassword'] ?? '';

            if (empty($username) || empty($email)) {
                echo "Username and email are required.";
                return;
            }

            if (!empty($password)) {
                if ($password !== $confirmPassword) {
                    echo "Passwords do not match.";
                    return;
                }
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            } else {
                $hashedPassword = $teacher['password']; // Keep the old password if not changing
            }

            $result = $this->userModel->updateTeacher($id, $username, $email, $hashedPassword);

            if ($result) {
                $_SESSION['message'] = "Teacher updated successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/teacher/edit/' . $id);
                exit;
            } else {
                $_SESSION['message'] = "Error updating teacher.";
                $_SESSION['status'] = "danger";
                header('Location: /admin/teacher/edit/' . $id);
                exit;
            }
        }

        $content = $this->render('admin/teacher/edit', ['teacher' => $teacher]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function delete($id)
    {
        $result = $this->userModel->deleteTeacher($id);

        if ($result) {
            $_SESSION['message'] = "Teacher deleted successfully!";
            $_SESSION['status'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting teacher.";
            $_SESSION['status'] = "danger";
        }

        header('Location: /admin/teacher/list');
        exit;
    }
}
