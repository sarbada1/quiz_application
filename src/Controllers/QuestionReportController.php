<?php
// QuestionReportController.php
namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\QuestionReportModel;
use PDO;

class QuestionReportController extends Controller
{
    private $reportModel;

    public function __construct(PDO $pdo)
    {
        $this->reportModel = new QuestionReportModel($pdo);
    }

    public function submitReport()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = "Please login to report questions.";
            $_SESSION['status'] = "danger";
            header('Location: /');
            exit;
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '/';

        $data = [
            'question_id' => $_POST['question_id'],
            'user_id' => $_SESSION['user_id'],
            'reason' => $_POST['reason'],
            'description' => $_POST['description']
        ];

        if ($this->reportModel->createReport($data)) {
            $_SESSION['message'] = "Question reported successfully.";
            $_SESSION['status'] = "success";
        } else {
            $_SESSION['message'] = "Failed to submit report.";
            $_SESSION['status'] = "danger";
        }

        // Redirect back to the page where report was initiated
        header('Location: ' . $referer);
        exit;
    }

    public function viewReports()
    {
     

        $reports = $this->reportModel->getAllReports();
        $content = $this->render('admin/reports/view', ['reports' => $reports]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function updateReportStatus($reportId)
    {
        try {
            // Get POST data
            $data = json_decode(file_get_contents('php://input'), true);
            $status = $data['status'] ?? null;
    
            if (!$status) {
                http_response_code(400);
                echo json_encode(['error' => 'Status is required']);
                return;
            }
    
            // Validate status value
            $validStatuses = ['pending', 'reviewed', 'resolved'];
            if (!in_array($status, $validStatuses)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid status']);
                return;
            }
    
            if ($this->reportModel->updateStatus($reportId, $status)) {
                echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
            } else {
                throw new \Exception('Database update failed');
            }
        } catch (\Exception $e) {
            error_log('Status update error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update status: ' . $e->getMessage()]);
        }
    }
}