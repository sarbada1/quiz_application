<?php
$questionsPerPage = 10;
$totalQuestions = count($questions);
$totalPages = ceil($totalQuestions / $questionsPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$startIndex = ($currentPage - 1) * $questionsPerPage;
$paginatedQuestions = array_slice($questions, $startIndex, $questionsPerPage);
?>
<?php if (isset($_SESSION['message'])): ?>
    <div id="alert" class="alert mt-20 w-90 alert-<?= $_SESSION['status'] ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= $_SESSION['message'] ?>
    </div>
    <?php unset($_SESSION['message']);
    unset($_SESSION['status']); ?>
<?php endif; ?>
<div class="container">
    <h2><?= htmlspecialchars($quiz['title']) ?></h2>
    <div class="quiz-info">
        <p><?= htmlspecialchars($quiz['description']) ?></p>
        <p>Total Marks: <?= $quiz['total_marks'] ?></p>
        <p>Number of Questions: <?= $totalQuestions ?></p>
    </div>

    <?php if (!empty($paginatedQuestions)): ?>
        <form id="quizForm">
            <?php foreach ($paginatedQuestions as $index => $question): ?>
                <div class="question-card" id="question-<?= $index ?>">
                    <div class="question-number"></div>
                    <h3><?= ($startIndex + $index + 1) ?>. <?= htmlspecialchars($question['question_text']) ?></h3>

                    <div class="options">
                        <?php if (!empty($question['answers'])): ?>
                            <?php foreach ($question['answers'] as $answer): ?>
                                <label class="option">
                                    <input type="radio" name="question_<?= $index ?>" value="<?= htmlspecialchars($answer['answer_text']) ?>" data-correct="<?= $answer['is_correct'] ?>">
                                    <?= htmlspecialchars($answer['answer_text']) ?>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No answers available for this question.</p>
                        <?php endif; ?>
                    </div>

                    <?php
                    $correctAnswer = '';
                    foreach ($question['answers'] as $answer) {
                        if ($answer['is_correct']) {
                            $correctAnswer = $answer['answer_text'];
                            break;
                        }
                    }
                    ?>

                    <div class="answer-section" id="answer-<?= $index ?>" style="display: none;">
                        <div class="answer">
                            <strong>Answer:</strong> <span class="correct-answer"><?= htmlspecialchars($correctAnswer) ?></span>
                        </div>

                    </div>

                    <div class="action-buttons">
                        <button type="button" class="view-answer" onclick="toggleAnswer(<?= $index ?>)">
                            <i class="fas fa-eye" style="margin-right:5px"></i> View Answer
                        </button>

                        <button type="button" class="report-question" onclick="reportQuestion(<?= $question['id'] ?>)">
                            <i class="fas fa-exclamation-triangle" style="color:#F7C804;margin-right:5px"></i> Report Question
                        </button>
                    </div>

                </div>
            <?php endforeach; ?>
        </form>

        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>">&laquo; Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" <?= $i === $currentPage ? 'class="active"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>No questions available for this quiz.</p>
    <?php endif; ?>
    <div id="reportQuestionModal" class="modal">

        <div class="modal-content">
            <span class="close" data-modal="reportQuestionModal">&times;</span>
            <h2>Report Question</h2>
            <form id="reportForm" method="POST" action="<?= $url('previous_question/report') ?>">
                <input type="hidden" id="reportQuestionId" name="question_id">

                <label for="reportReason">Reason for Report:</label>
                <select id="reportReason" name="reason" required>
                    <option value="">Select a reason</option>
                    <option value="no_correct_answer">No Correct Answer</option>
                    <option value="multiple_correct">Multiple Correct Answers</option>
                    <option value="unclear">Question is Unclear</option>
                    <option value="other">Other</option>
                </select>

                <label for="reportDescription">Additional Details:</label>
                <textarea id="reportDescription" name="description" rows="4"></textarea>

                <button type="submit" class="btn btn-primary">Submit Report</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.option input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const questionId = this.name.split('_')[1];
                const selectedOption = this.value;
                const isCorrect = this.dataset.correct === '1';

                document.querySelectorAll(`input[name="question_${questionId}"]`).forEach(option => {
                    option.disabled = true;
                    const feedbackIcon = document.createElement('i');
                    feedbackIcon.style.marginLeft = '10px';

                    if (option.value === selectedOption) {
                        if (isCorrect) {
                            feedbackIcon.className = 'fas fa-check-circle';
                            feedbackIcon.style.color = 'green';
                        } else {
                            feedbackIcon.className = 'fas fa-times-circle';
                            feedbackIcon.style.color = 'red';
                        }
                        option.parentElement.appendChild(feedbackIcon);
                    }
                });
            });
        });
        document.querySelectorAll('.close').forEach(function(closeBtn) {
            closeBtn.onclick = function() {
                const modalId = this.getAttribute('data-modal');
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.display = 'none';
                }
            }
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    });

    function toggleAnswer(questionId) {
        const answerSection = document.getElementById(`answer-${questionId}`);
        answerSection.style.display = answerSection.style.display === 'none' ? 'block' : 'none';
    }

    function reportQuestion(questionId) {
        const modal = document.getElementById('reportQuestionModal');
        const questionIdInput = document.getElementById('reportQuestionId');
        if (modal && questionIdInput) {
            questionIdInput.value = questionId;
            modal.style.display = 'block';
        }
    }
</script>

<style>
    .container {
        width: 90%;
        margin: 80px auto;
    }

    .question-card {
        background: white;
        padding: 20px;
        margin-bottom: 30px;
        border-bottom: 1px solid #eee;
    }

    .options {
        margin: 20px 0;
    }

    .option {
        display: block;
        padding: 10px;
        margin: 10px 0;
        cursor: pointer;
    }

    .option input[type="radio"] {
        margin-right: 10px;
    }

    .answer-section {
        background-color: #f0f9f0;
        padding: 15px;
        margin: 15px 0;
        border-radius: 5px;
    }

    .answer {
        color: #28a745;
        margin-bottom: 10px;
    }

    .explanation {
        color: #666;
    }

    .action-buttons {
        width: 450px;
        margin-top: 20px;
        display: flex;
        gap: 15px;
    }

    .action-buttons button {
        background: none;
        border: none;
        color: #007bff;
        cursor: pointer;
        display: flex;
        align-items: center;
        padding: 5px 10px;
        font-size: 14px;
    }

    .action-buttons button i {
        margin-right: 5px;
    }

    .view-answer:hover {
        color: #0056b3;
    }

    .discuss-forum {
        color: #17a2b8;
    }

    .discuss-forum:hover {
        color: #117a8b;
    }

    .report-question {
        color: #ffc107;
    }

    .report-question:hover {
        color: #d39e00;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .pagination a {
        margin: 0 5px;
        padding: 10px 15px;
        text-decoration: none;
        color: #007bff;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .pagination a.active {
        background-color: #007bff;
        color: white;
        border: 1px solid #007bff;
    }

    .pagination a:hover {
        background-color: #0056b3;
        color: white;
        border: 1px solid #0056b3;
    }
</style>