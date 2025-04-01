<?php

namespace MVC\Controllers;

use PDO;
use Exception;
use ZipArchive;
use MVC\Controller;
use MVC\Models\CategoryModel;
use MVC\Models\LevelModel;
use MVC\Models\QuizModel;
use MVC\Models\TagModel;

class QuestionImportController extends Controller
{
    public $pdo;
    public $tagModel;
    public $categoryModel;
    public $levelModel;
    private $requiredColumns = [
        'quiz_id',
        'question_text',
        'question_type',
        'answer_1',
        'is_correct_1',
        'reason_1',
        'answer_2',
        'is_correct_2',
        'reason_2',
        'answer_3',
        'is_correct_3',
        'reason_3',
        'answer_4',
        'is_correct_4',
        'reason_4'
    ];
    private $headerMap = [
        'quiz_id' => ['quiz_id', 'quizid', 'quiz'],
        'question_text' => ['question_text', 'question', 'questiontext'],
        'question_type' => ['question_type', 'questiontype', 'type'],
        'answer_1' => ['answer_1', 'answer1', 'answer 1'],
        'is_correct_1' => ['is_correct_1', 'correct1', 'iscorrect1'],
        'reason_1' => ['reason_1', 'reason1', 'explanation1'],
        'answer_2' => ['answer_2', 'answer2', 'answer 2'],
        'is_correct_2' => ['is_correct_2', 'correct2', 'iscorrect2'],
        'reason_2' => ['reason_2', 'reason2', 'explanation2'],
        'answer_3' => ['answer_3', 'answer3', 'answer 3'],
        'is_correct_3' => ['is_correct_3', 'correct3', 'iscorrect3'],
        'reason_3' => ['reason_3', 'reason3', 'explanation3'],
        'answer_4' => ['answer_4', 'answer4', 'answer 4'],
        'is_correct_4' => ['is_correct_4', 'correct4', 'iscorrect4'],
        'reason_4' => ['reason_4', 'reason4', 'explanation4']
    ];

    public function downloadTemplate()
    {
        // Set headers for Excel download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="question_import_template.csv"');

        // Fetch available quizzes
        $quizzes = $this->pdo->query("SELECT id, title FROM quizzes ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

        // Fetch question types
        $questionTypes = $this->pdo->query("SELECT id, type FROM question_type ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add reference information as comments
        $instructions = [
            "Available Quizzes (use quiz_id from below):",
            array_map(function ($quiz) {
                return "ID: {$quiz['id']} - {$quiz['title']}";
            }, $quizzes),
            "",
            "Question Types (use type_id from below):",
            array_map(function ($type) {
                return "ID: {$type['id']} - {$type['type']}";
            }, $questionTypes),
            "",
            "Instructions:",
            "1. quiz_id: Use one of the quiz IDs listed above",
            "2. question_type: Use one of the type IDs listed above",
            "3. is_correct_X: Use 1 for correct answer, 0 for incorrect",
            "4. All text fields can contain quotes",
            "",
        ];

        // Write instructions as comments
        foreach ($instructions as $line) {
            if (is_array($line)) {
                foreach ($line as $subline) {
                    fwrite($output, "# " . $subline . "\n");
                }
            } else {
                fwrite($output, "# " . $line . "\n");
            }
        }

        // Add headers
        fputcsv($output, $this->requiredColumns);

        // Add sample data
        $sampleData = [
            [
                1,                                          // quiz_id
                'What is the capital of France?',          // question_text
                1,                                         // question_type
                'Paris',                                   // answer_1
                1,                                         // is_correct_1
                'Paris is the capital of France',          // reason_1
                'London',                                  // answer_2
                0,                                         // is_correct_2
                'London is the capital of UK',             // reason_2
                'Berlin',                                  // answer_3
                0,                                         // is_correct_3
                'Berlin is the capital of Germany',        // reason_3
                'Rome',                                    // answer_4
                0,                                         // is_correct_4
                'Rome is the capital of Italy'             // reason_4
            ]
        ];

        foreach ($sampleData as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->quizModel = new QuizModel($pdo);
        $this->tagModel = new TagModel($pdo);
        $this->categoryModel = new CategoryModel($pdo);
        $this->levelModel = new LevelModel($pdo);
    }
    public function index()
    {
        $sampleCsv = $this->generateSampleCsv();
        $content = $this->render('admin/question/import', [
            'sampleCsv' => $sampleCsv
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function indexword($id=null)
    {
        // $sampleCsv = $this->generateSampleCsv();
        $quizzes = $this->quizModel->getAll();
        $tags = $this->tagModel->getAllTags();
        $categories = $this->categoryModel->getAllCategories();
        $levels = $this->levelModel->getAll();

        $content = $this->render('admin/question/word', [
            'quizzes' => $quizzes,
            'tags' => $tags,
            'categories' => $categories,
            'levels' => $levels,
            'id' => $id??''
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    private function generateSampleCsv()
    {
        // Headers
        $headers = implode(',', $this->requiredColumns);

        // Sample data rows
        $sampleRows = [
            // Row 1 - Multiple choice question
            implode(',', [
                '1',                                           // quiz_id
                '"What is the capital of France?"',           // question_text
                '1',                                          // question_type
                '"Paris"',                                    // answer_1
                '1',                                          // is_correct_1
                '"Paris is the capital of France"',           // reason_1
                '"London"',                                   // answer_2
                '0',                                          // is_correct_2
                '"London is the capital of UK"',              // reason_2
                '"Berlin"',                                   // answer_3
                '0',                                          // is_correct_3
                '"Berlin is the capital of Germany"',         // reason_3
                '"Rome"',                                     // answer_4
                '0',                                          // is_correct_4
                '"Rome is the capital of Italy"'              // reason_4
            ]),
            // Row 2 - Another example
            implode(',', [
                '1',                                           // quiz_id
                '"Which planet is known as the Red Planet?"',  // question_text
                '1',                                           // question_type
                '"Mars"',                                      // answer_1
                '1',                                           // is_correct_1
                '"Mars appears red due to iron oxide"',        // reason_1
                '"Venus"',                                     // answer_2
                '0',                                           // is_correct_2
                '"Venus is not red in color"',                 // reason_2
                '"Jupiter"',                                   // answer_3
                '0',                                           // is_correct_3
                '"Jupiter is the largest planet"',             // reason_3
                '"Saturn"',                                    // answer_4
                '0',                                           // is_correct_4
                '"Saturn is known for its rings"'              // reason_4
            ])
        ];

        // Combine headers and sample rows with line breaks
        return $headers . "\n" . implode("\n", $sampleRows);
    }
    private function normalizeHeader($header)
    {
        $header = strtolower(trim($header));
        $header = str_replace(' ', '_', $header);

        foreach ($this->headerMap as $standardHeader => $variations) {
            if (in_array($header, $variations)) {
                return $standardHeader;
            }
        }
        return $header;
    }
    private function validateCsvStructure($headers)
    {
        // Normalize headers
        $normalizedHeaders = array_map([$this, 'normalizeHeader'], $headers);

        // Check for missing columns
        $missingColumns = array_diff($this->requiredColumns, $normalizedHeaders);
        if (!empty($missingColumns)) {
            throw new Exception(
                "Missing required columns: " . implode(', ', $missingColumns) . "\n" .
                    "Expected format: " . $this->generateSampleCsv()
            );
        }

        return $normalizedHeaders;
    }

    private function validateQuizId($quizId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM quizzes WHERE id = ?");
        $stmt->execute([$quizId]);
        return $stmt->fetchColumn() > 0;
    }

    private function cleanRow($row)
    {
        return array_map(function ($value) {
            return trim(str_replace(['"', "'"], '', $value));
        }, $row);
    }

    public function import()
    {
        try {
            if (!isset($_FILES['csv_file'])) {
                throw new Exception("No file uploaded");
            }

            $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
            if (!$file) {
                throw new Exception("Could not open file");
            }

            // Validate CSV structure
            $headers = $this->cleanRow(fgetcsv($file));
            $this->validateCsvStructure($headers);

            $this->pdo->beginTransaction();
            $rowCount = 0;
            $lineNumber = 2; // Starting after header

            while (($rawRow = fgetcsv($file)) !== false) {
                $row = $this->cleanRow($rawRow);
                $quizId = intval($row[array_search('quiz_id', $headers)]);

                if (!$this->validateQuizId($quizId)) {
                    throw new Exception("Invalid quiz ID {$quizId} at row {$lineNumber}");
                }

                // Insert question
                $stmt = $this->pdo->prepare("
                    INSERT INTO questions (quiz_id, question_text, question_type) 
                    VALUES (:quiz_id, :question_text, :question_type)
                ");

                $stmt->execute([
                    ':quiz_id' => $quizId,
                    ':question_text' => $row[array_search('question_text', $headers)],
                    ':question_type' => intval($row[array_search('question_type', $headers)])
                ]);

                $questionId = $this->pdo->lastInsertId();

                // Insert answers
                for ($i = 1; $i <= 4; $i++) {
                    $answerText = $row[array_search("answer_{$i}", $headers)] ?? '';
                    if (!empty($answerText)) {
                        $stmt = $this->pdo->prepare("
                            INSERT INTO answers (question_id, answer, isCorrect, reason)
                            VALUES (:question_id, :answer, :is_correct, :reason)
                        ");

                        $stmt->execute([
                            ':question_id' => $questionId,
                            ':answer' => $answerText,
                            ':is_correct' => intval($row[array_search("is_correct_{$i}", $headers)]),
                            ':reason' => $row[array_search("reason_{$i}", $headers)] ?? null
                        ]);
                    }
                }
                $rowCount++;
                $lineNumber++;
            }

            $this->pdo->commit();
            fclose($file);

            $_SESSION['message'] = "Successfully imported {$rowCount} questions";
            $_SESSION['status'] = "success";
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $_SESSION['message'] = "Error importing questions: " . $e->getMessage();
            $_SESSION['status'] = "danger";
        }

        header('Location: /admin/question/import');
        exit;
    }

    public function importText()
    {
        try {
            $content = trim($_POST['question_content']);
            $tagIds = $_POST['tags'] ?? [];
            $quiz_id = $_POST['quiz_id'] ?? null;
    
            // Optional fields
            $category_id = $_POST['category_id'] ?? '';
            $difficulty_level = $_POST['difficulty_level'] ?? '';
            $question_type = $_POST['question_type'] ?? '';
            $year = $_POST['year'] ?? '';
            $marks = $_POST['marks'] ?? 1;
    
            $parts = preg_split('/\bAnswers\b/i', $content, 2);
            if (count($parts) !== 2) {
                throw new Exception('Invalid format - missing Answers section');
            }
            // echo '<pre>';
            // print_r($parts);
            // die;
            // print_r($questions);
            // print_r($answers);
            $questions = $this->parseQuestions($parts[0]);
            $answers = $this->parseAnswers($parts[1]);
          
    
            if (empty($questions)) {
                throw new Exception('No valid questions found');
            }
    
            $this->pdo->beginTransaction();
            $inserted = 0;
    
            foreach ($questions as $question) {
                if (empty($question['options'])) {
                    continue;
                }
    
                if ($quiz_id) {
                    // Insert into previous_year_questions table if quiz_id is provided
                    $stmt = $this->pdo->prepare("
                        INSERT INTO previous_year_questions (quiz_id, question_text, year) 
                        VALUES (:quiz_id, :question_text, :year)
                    ");
    
                    $stmt->execute([
                        ':quiz_id' => $quiz_id,
                        ':question_text' => $question['text'],
                        ':year' => $year
                    ]);
    
                    $question_id = $this->pdo->lastInsertId();
                } else {
                    // Insert into the default questions table
                    $stmt = $this->pdo->prepare("
                        INSERT INTO questions (question_text, category_id, difficulty_level, marks, question_type, year) 
                        VALUES (:question_text, :category_id, :difficulty_level, :marks, :question_type, :year)
                    ");
    
                    $stmt->execute([
                        ':question_text' => $question['text'],
                        ':category_id' => $category_id,
                        ':difficulty_level' => $difficulty_level,
                        ':marks' => $marks,
                        ':question_type' => $question_type,
                        ':year' => $year
                    ]);
    
                    $question_id = $this->pdo->lastInsertId();
                }
    
                // Handle tags
                if ($question_id && !empty($tagIds)) {
                    $this->handleTags($tagIds, $question_id);
                }
    
                // Insert answers
                foreach ($question['options'] as $letter => $text) {
                    $is_correct = (isset($answers[$question['number']]) &&
                        $answers[$question['number']] === $letter) ? 1 : 0;
    
                    if ($quiz_id) {
                        // Insert into previous_year_answers table if quiz_id is provided
                        $stmt = $this->pdo->prepare("
                            INSERT INTO previous_year_answers (question_id, answer, isCorrect) 
                            VALUES (:question_id, :answer, :is_correct)
                        ");
                    } else {
                        // Insert into the default answers table
                        $stmt = $this->pdo->prepare("
                            INSERT INTO answers (question_id, answer, isCorrect) 
                            VALUES (:question_id, :answer, :is_correct)
                        ");
                    }
    
                    $stmt->execute([
                        ':question_id' => $question_id,
                        ':answer' => $text,
                        ':is_correct' => $is_correct
                    ]);
                }
                $inserted++;
            }
    
            $this->pdo->commit();
            $_SESSION['message'] = "Successfully imported $inserted questions";
            $_SESSION['status'] = "success";
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $_SESSION['message'] = "Error importing questions: " . $e->getMessage();
            $_SESSION['status'] = "danger";
        }
    
        header('Location: /admin/question/word');
        exit();
    }

    private function parseQuestions($content)
    {
        $questions = [];
        $content = preg_replace('/\r\n|\r/', "\n", $content);
        
        $pattern = '/(\d+)\.\s+(.*?)(?=\d+\.\s+|$)/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $number = (int)$match[1];
            $questionBlock = $match[2];
            
            if (preg_match('/(.*?)a\)/s', $questionBlock, $textMatches)) {
                $questionText = trim($textMatches[1]);
                
                $options = [];
                preg_match_all('/([a-d])\)\s*([^\n]+)/', $questionBlock, $optionMatches, PREG_SET_ORDER);
                
                foreach ($optionMatches as $option) {
                    $letter = strtolower($option[1]);
                    $text = trim($option[2]);
                    $options[$letter] = $text;
                }
                
                if (count($options) === 4) { 
                    $questions[] = [
                        'number' => $number,
                        'text' => $questionText,
                        'options' => $options
                    ];
                }
            }
        }
        
        error_log("Found " . count($questions) . " questions");
        return $questions;
    }
    
    private function parseAnswers($content)
    {
        $answers = [];
        
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (preg_match('/(\d+)[.\s]+([a-d])/i', $line, $match)) {
                $number = (int)$match[1];
                $answer = strtolower(trim($match[2]));
                $answers[$number] = $answer;
            }
        }
        
        error_log("Found " . count($answers) . " answers");
        return $answers;
    }
    private function handleTags($tagIds, $questionId) {
        // Insert question-tag relationships
        if (!empty($tagIds)) {
            $values = array_map(function($tagId) use ($questionId) {
                return "($questionId, $tagId)";
            }, array_unique($tagIds));
            
            $sql = "INSERT IGNORE INTO question_tags (question_id, tag_id) VALUES " . 
                   implode(',', $values);
                   
            $this->pdo->exec($sql);
        }
    }


}
