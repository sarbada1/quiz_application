<?php

namespace MVC\Controllers;

use PDO;
use MVC\Controller;
use MVC\Models\User;
use MVC\Validators\Validator;

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
            try {
                $data = [
                    'username' => trim($_POST['username'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'password' => $_POST['password'] ?? '',
                    'cpassword' => $_POST['cpassword'] ?? ''
                ];
    
                // Validate data
                $this->validateTeacherData($data);
    
                // Check if email already exists
                $user = new User($this->pdo);
                if ($user->isEmailExists($data['email'])) {
                    throw new \Exception('Email already registered');
                }
    
                // Password hashing
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
                // Insert teacher data
                $result = $user->insert([
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => $hashedPassword,
                    'usertype_id' => 2,
                    'is_verified' => 1,
                ]);
    
                if ($result) {
                    $_SESSION['message'] = "Teacher added successfully!";
                    $_SESSION['status'] = "success";
                    header('Location: /admin/teacher/list');
                    exit();
                }
    
            } catch (\Exception $e) {
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['status'] = "danger";

                header('Location: /admin/teacher/add');
                exit();
            }
        }
    
        // Show the form
        echo $this->render('admin/teacher/add');
    }
    
    private function validateTeacherData($data)
    {
        $validator = new Validator($data, [
            'username' => ['required', 'min:3', 'alpha_space'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
            'cpassword' => ['required', 'same:password']
        ]);
    
        if (!$validator->validate()) {
            throw new \Exception($validator->getFirstError());
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
        $teachers = $userModel->get([
            ['field' => 'usertype_id', 'operator' => '=', 'value' => 3],
            ['field' => 'is_verified', 'operator' => '=', 'value' => 1]

        ]);
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
