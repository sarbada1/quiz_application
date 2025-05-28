<style>
     
    .category-section {
        background: white;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .category-title {
        color: #2c3e50;
        font-size: 1.2rem;
        padding-bottom: 15px;
        margin-bottom: 20px;
        border-bottom: 2px solid #eee;
    }

    .palette-buttons {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
        margin-top: 15px;
    }

    .palette-btn {
        width: 40px;
        height: 40px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: white;
        cursor: pointer;
        color: black;
        transition: all 0.3s ease;
    }

    .palette-btn.attempted {
        background: #4CAF50;
        color: white;
        border-color: #4CAF50;
    }

    .palette-info {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }

    .palette-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
    }

    .palette-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .palette-dot.not-visited {
        background: #f8f9fa;
    }

    .palette-dot.attempted {
        background: #4CAF50;
    }

    .review-container {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .question-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .question-block {
        display: block !important;
        /* Force show all questions */
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .question-palette {
        position: sticky;
        top: 100px;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .question-review {
        border: 1px solid #ddd;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
    }

    .question-review.correct {
        border-left: 4px solid #4CAF50;
    }

    .question-review.incorrect {
        border-left: 4px solid #f44336;
    }

    .answer {
        padding: 10px;
        margin: 5px 0;
        border-radius: 4px;
    }

    .answer.correct {
        background: #e8f5e9;
        border: 1px solid #4CAF50;
    }

    .answer.wrong {
        background: #ffebee;
        border: 1px solid #f44336;
    }

    .explanation {
        margin-top: 15px;
        padding: 15px;
        background: #fff3e0;
        border-radius: 4px;
    }


    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 8px;
        max-width: 600px;
        width: 90%;
    }

    .performance-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin: 20px 0;
    }

    .stat {
        text-align: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .stat span {
        color: #666;
        font-size: 0.9rem;
    }

    .stat strong {
        display: block;
        font-size: 1.5rem;
        color: #2c3e50;
        margin-top: 5px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 20px;
    }

    .review-btn,
    .dashboard-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .review-btn {
        background: #2ecc71;
        color: white;
    }

    .dashboard-btn {
        background: #3498db;
        color: white;
    }

    .submit-btn {
        background: green;
    }

    .review-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1001;
        overflow-y: auto;
    }

    .review-modal-content {
        background: white;
        margin: 20px auto;
        padding: 30px;
        width: 90%;
        max-width: 800px;
        border-radius: 8px;
        position: relative;
    }

    .review-modal-content .close {
        position: absolute;
        top: 0;
        right: 30px;
        font-size: 50px;
        cursor: pointer;
    }

    .review-question {
        margin-bottom: 20px;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #ddd;
    }

    .review-answer {
        padding: 15px;
        margin: 10px 0;
        border-radius: 8px;
        border: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .review-answer.correct {
        background: #e8f5e9;
        border: 2px solid #4CAF50;
    }

    .review-answer.wrong {
        background: #ffebee;
        border: 2px solid #f44336;
    }

    .review-answer.correct-answer {
        background: #e8f5e9;
        border: 2px solid #4CAF50;
        margin-top: 5px;
        font-weight: bold;
    }

    .status {
        font-weight: bold;
        margin-left: 10px;
    }

    .status.correct {
        color: #4CAF50;
    }

    .status.wrong {
        color: #f44336;
    }

    .review-question {
        margin-bottom: 20px;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .question-text {
        font-size: 1.1rem;
        margin-bottom: 15px;
        color: #2c3e50;
    }

    .review-question.correct {
        border-left-color: #4CAF50;
    }

    .review-question.incorrect {
        border-left-color: #f44336;
    }

    .answers {
        margin-top: 10px;
    }

    .user-answer,
    .correct-answer {
        padding: 10px;
        margin: 5px 0;
        border-radius: 4px;
    }

    .user-answer-correct {
        border-color: green;
        background-color: #d4edda;
    }

    .user-answer-wrong {
        border-color: red;
        background-color: #f8d7da;
    }

    .correct-answer {
        border-color: green;
        background-color: #d4edda;
    }

    .answer-label {
        font-weight: bold;
        margin-right: 10px;
    }

    .question-page {
        display: none;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin: 20px 0;
        align-items: center;
    }

    .pagination button {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        background: #3498db;
        color: white;
        cursor: pointer;
    }

    .pagination button:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    #pageInfo {
        font-size: 1.1rem;
    }

    .timer {
        font-size: 1.2rem;
        font-weight: bold;
        color: #2c3e50;
        padding: 10px;
    }

    /* Remove progress bar */
    .timer-progress {
        display: none;
    }

    .instructions-panel {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .instructions-panel h3 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .instructions-panel ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .instructions-panel li {
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #555;
        font-size: 0.9rem;
    }

    .instructions-panel ul ul {
        margin-left: 25px;
        margin-top: 8px;
    }

    .color-box {
        display: inline-block;
        width: 15px;
        height: 15px;
        border-radius: 3px;
        margin-right: 5px;
    }

    .color-box.not-visited {
        background: #e9ecef;
        border: 1px solid #dee2e6;
    }

    .color-box.answered {
        background: #28a745;
    }

    .color-box.not-answered {
        background: #dc3545;
    }

    .color-box.marked {
        background: #ffc107;
    }

    .instructions-panel i {
        color: #3498db;
        width: 20px;
    }
</style>

<body>

    <?php if (isset($_SESSION['message'])): ?>
        <div id="alert" class="alert mt-20 w-75 alert-<?= $_SESSION['status'] ?>" role="alert">
            <button type="button" class="closealert" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message']);
        unset($_SESSION['status']); ?>
    <?php endif; ?>

    <div id="quizModal" class="modal">
        <div class="modal-content">
            <span class="close" data-modal="quizModal">&times;</span>
            <p>Please log in to start the quiz.</p>
            <button class="bg-primary text-dark text-lg" id="startQuiz">Login</button>
        </div>
    </div>
    <?php include __DIR__ . '/auth/login.php'; ?>
    <?php include __DIR__ . '/auth/register.php'; ?>
    <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
        <div class="test-container" id="testContainer" data-set-id="<?= $set['id'] ?>">
            <!-- Header Section -->

        <div class="header">
    <div class="timer-container">
    <div class="timer" id="timer">
        <i class="fas fa-clock"></i> <span id="time-display">00:00:00</span>
    </div>
</div>
    <div class="timer-progress" id="timerProgress"></div>
    <button id="submitBtn" class="submit-btn" onclick="submitTest()">Submit Test</button>
    
    <div class="test-info">
        <h3><?= htmlspecialchars($quiz['title']) ?> - Set <?= htmlspecialchars($set['set_name']) ?></h3>
        <p>Total Marks: <?= $totalMarks ?></p>
    </div>
</div>


            <div class="main-content mt-20">
                <!-- Questions Section -->
                <?php
                // Group questions by category
                $groupedQuestions = [];
                foreach ($questions as $question) {
                    $categoryId = $question['category_id'];
                    if (!isset($groupedQuestions[$categoryId])) {
                        $groupedQuestions[$categoryId] = [
                            'name' => $question['category_name'],
                            'questions' => []
                        ];
                    }
                    $groupedQuestions[$categoryId]['questions'][] = $question;
                }
                $globalIndex = 1;
                ?>
                <div class="question-list">
                    <?php foreach ($groupedQuestions as $categoryId => $category): ?>
                        <div class="category-section">
                            <h4 class="category-title"><?= htmlspecialchars($category['name']) ?></h4>

                            <?php foreach ($category['questions'] as $index => $question): ?>
                                <div class="question-block" id="question_<?= $globalIndex ?>"
                                    data-question-id="<?= $question['id'] ?>">
                                    <div class="question-header">
                                        <span class="question-number">Q<?= $globalIndex ?>.</span>
                                        <span class="marks">[<?= $question['marks'] ?> marks]</span>
                                        <button class="btn-report" onclick="reportQuestion(<?= $question['id'] ?>)">
                                            <i class="fas fa-flag"></i> Report Question
                                        </button>
                                    </div>

                                    <div class="question-text">
                                        <?= htmlspecialchars($question['question_text']) ?>
                                    </div>

                                    <div class="options">
                                        <?php foreach ($question['answers'] as $answer): ?>
                                            <label class="option">
                                                <input type="radio"
                                                    name="question_<?= $question['id'] ?>"
                                                    value="<?= $answer['id'] ?>"
                                                    data-correct="<?= $answer['isCorrect'] ? 'true' : 'false' ?>"
                                                    onclick="submitAnswer(<?= $answer['id'] ?>, <?= $question['id'] ?>, <?= $globalIndex ?>)">
                                                <?= htmlspecialchars($answer['answer']) ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php $globalIndex++;
                                ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Question Palette -->
                <div class="question-palette">
                    <h3>Question Palette</h3>
                    <div class="palette-buttons">
                        <?php
                        $paletteIndex = 1;
                        foreach ($groupedQuestions as $category):
                            foreach ($category['questions'] as $question): ?>
                                <button id="palette_<?= $paletteIndex ?>"
                                    class="palette-btn not-visited"
                                    onclick="jumpToQuestion(<?= $paletteIndex ?>)">
                                    <?= $paletteIndex ?>
                                </button>
                        <?php
                                $paletteIndex++;
                            endforeach;
                        endforeach; ?>
                    </div>
                    <div class="instructions-panel">
                        <h3><i class="fas fa-info-circle"></i> Instructions</h3>
                        <ul>
                            <li><i class="fas fa-clock"></i> Timer starts automatically when you begin</li>
                            <li><i class="fas fa-palette"></i> Question Palette Colors:
                                <ul>
                                    <li><span class="color-box not-visited"></span> Not Visited</li>
                                    <li><span class="color-box answered"></span> Answered</li>
                                </ul>
                            </li>
                            <li><i class="fas fa-mouse-pointer"></i> Click question numbers to navigate</li>
                            <li><i class="fas fa-flag"></i> Use 'Review Answers' for later revision</li>
                            <li><i class="fas fa-check-circle"></i> Submit test when finished</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <div id="reportQuestionModal" class="modal">

        <div class="modal-content">
            <span class="close" data-modal="reportQuestionModal">&times;</span>
            <h2>Report Question</h2>
            <form id="reportForm" method="POST" action="<?= $url('question/report') ?>">
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
    <div id="reviewModal" class="review-modal">
        <div class="review-modal-content">

            <h2>Review Answers</h2>
            <div class="review-questions"></div>
        </div>
    </div>
</body>

<script>


// function initializeTimer() {
//     // Get duration from PHP in minutes or use default (60 minutes)
//     const durationMinutes = <?= isset($quiz['duration']) ? intval($quiz['duration']) : 60 ?>;
//     let timeLeft = durationMinutes * 60; // Convert to seconds for countdown
//     console.log("Quiz duration:", durationMinutes, "minutes");

//     const timerDisplay = document.getElementById('timer');
//     console.log("Timer display element:", timerDisplay);

//     // Make sure timer element exists
//     if (!timerDisplay) {
//         console.error('Timer display element not found');
//         return;
//     }

//     // Clear any existing timer
//     if (window.quizTimer) {
//         clearInterval(window.quizTimer);
//     }

//     // Set initial display
//     updateTimerDisplay();

//     // Start countdown
//     window.quizTimer = setInterval(() => {
//         timeLeft--;

//         updateTimerDisplay();

//         // Warning for last 5 minutes
//         if (timeLeft <= 300) {
//             timerDisplay.style.color = '#e74c3c';
//             timerDisplay.style.animation = 'pulse 1s infinite';
//         }

//         // Auto-submit when time is up
//         if (timeLeft <= 0) {
//             clearInterval(window.quizTimer);
//             submitTest();
//         }
//     }, 1000);

//     // Helper function to update timer display
//     function updateTimerDisplay() {
//         const hours = Math.floor(timeLeft / 3600);
//         const minutes = Math.floor((timeLeft % 3600) / 60);
//         const seconds = timeLeft % 60;

//         timerDisplay.innerHTML = `
//             <i class="fas fa-clock"></i>
//             ${hours > 0 ? hours + ':' : ''}${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}
//         `;
//     }
// }

function initializeTimer() {
    // Get duration from PHP in minutes or use default (60 minutes)
    const durationMinutes = <?= isset($quiz['duration']) ? intval($quiz['duration']) : 60 ?>;
    let timeLeft = durationMinutes * 60; // Convert to seconds for countdown
    
    // Get timer display element
    const timerDisplay = document.getElementById('time-display');
    const timerContainer = document.getElementById('timer');
    
    // Make sure timer elements exist
    if (!timerDisplay || !timerContainer) {
        console.error('Timer elements not found');
        return;
    }

    // Clear any existing timer
    if (window.quizTimer) {
        clearInterval(window.quizTimer);
    }

    // Set initial display
    updateTimerDisplay();

    // Start countdown
    window.quizTimer = setInterval(() => {
        timeLeft--;
        updateTimerDisplay();

        // Warning for last 5 minutes
        if (timeLeft <= 300) {  // 5 minutes = 300 seconds
            timerContainer.style.color = '#e74c3c';
            timerContainer.style.fontWeight = 'bold';
        }

        // Auto-submit when time is up
        if (timeLeft <= 0) {
            clearInterval(window.quizTimer);
            submitTest();
        }
    }, 1000);

    // Helper function to update timer display
    function updateTimerDisplay() {
        const hours = Math.floor(timeLeft / 3600);
        const minutes = Math.floor((timeLeft % 3600) / 60);
        const seconds = timeLeft % 60;

        timerDisplay.textContent = 
            `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
}
    let answeredQuestions = new Set();
    // let currentQuestion = 0;
    document.addEventListener('DOMContentLoaded', function() {

        const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

        if (!isLoggedIn) {
            document.getElementById('quizModal').style.display = 'block';
        }
        document.getElementById('testContainer').style.display = 'block';

        initializeTimer();

        showQuestion(currentQuestion);
        initializeQuestionPalette();
        // Close modal when clicking the close button
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
            if (event.target == document.getElementById('quizModal')) {
                document.getElementById('quizModal').style.display = 'none';
                window.history.back();
            }
        }
    });


    function reportQuestion(questionId) {
        const modal = document.getElementById('reportQuestionModal');
        const questionIdInput = document.getElementById('reportQuestionId');
        if (modal && questionIdInput) {
            questionIdInput.value = questionId;
            modal.style.display = 'block';
        }
    }


  


    function initializeQuestionPalette() {
        // Add event listeners to question options to mark as attempted
        const questions = document.querySelectorAll('.question');
        questions.forEach((question, index) => {
            const options = question.querySelectorAll('input[type="radio"]');
            options.forEach((option) => {
                option.addEventListener('change', () => markAttempted(index));
            });
        });

        // Add click event to question palette buttons
        const paletteButtons = document.querySelectorAll('.question-number-btn');
        paletteButtons.forEach((button, index) => {
            button.addEventListener('click', () => jumpToQuestion(index));
        });
    }



    function jumpToQuestion(index) {
        currentQuestion = index;
        showQuestion(currentQuestion);
    }




    // Add pulse animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    `;
    document.head.appendChild(style);
</script>
<script>
    let userAnswers = {};
    let currentQuestion = 1;
    let totalQuestions = <?= count($questions) ?>;
    let currentPage = 1;

    const timeLimit = <?= isset($quiz['time']) ? $quiz['time'] * 60 : 3600 ?>;
    let timeLeft = timeLimit;
    let timer;

    function showPage(pageNum) {
        document.querySelectorAll('.question-page').forEach(page => {
            page.style.display = 'none';
        });

        const targetPage = document.getElementById(`page_${pageNum}`);
        if (targetPage) {
            targetPage.style.display = 'block';
            currentPage = pageNum;
            document.getElementById('currentPage').textContent = pageNum;

            // Update button states
            document.getElementById('prevBtn').disabled = pageNum === 1;
            document.getElementById('nextBtn').disabled = pageNum === totalPages;
        }
    }

    function nextPage() {
        if (currentPage < totalPages) {
            showPage(currentPage + 1);
        }
    }

    function previousPage() {
        if (currentPage > 1) {
            showPage(currentPage - 1);
        }
    }



    function submitAnswer(answerId, questionId, questionNumber) {
        userAnswers[questionId] = answerId;

        // Update palette button
        const paletteBtn = document.getElementById(`palette_${questionNumber}`);
        if (paletteBtn) {
            paletteBtn.classList.remove('not-visited');
            paletteBtn.classList.add('attempted');
        }

        fetch('<?= $url('ajax/save-answer') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                questionId: questionId,
                answerId: answerId,
                questionNumber: questionNumber
            })
        });
    }



    function showReviewModal() {
        const modal = document.createElement('div');
        modal.className = 'review-modal';
        console.log(userAnswers);

        const reviewHTML = `
        <div class="review-modal-content">
            <span class="close" onclick="closeReviewModal()">&times;</span>
            <h2>Review Answers</h2>
            <div class="review-questions">
                ${Object.keys(userAnswers).map((questionId, index) => {
                    const question = document.querySelector(`[data-question-id="${questionId}"]`);
                    const questionText = question.querySelector('.question-text').innerHTML;
                    const options = Array.from(question.querySelectorAll('.option'));
                    
                    const userAnswerId = userAnswers[questionId];
                    
                    return `
                        <div class="review-question">
                            <div class="question-text">
                                <strong>Q${index + 1}.</strong> ${questionText}
                            </div>
                            <div class="answers">
                                ${options.map(option => {
                                    const input = option.querySelector('input');
                                    const isUserAnswer = input.value == userAnswerId;
                                    const isCorrect = input.getAttribute('data-correct') === "true";
                                    
                                    let optionClass = '';
                                    if (isUserAnswer) {
                                        optionClass = isCorrect ? 'user-answer-correct' : 'user-answer-wrong';
                                    } else if (isCorrect) {
                                        optionClass = 'correct-answer';
                                    }
                                    
                                    return `
                                        <div class="option ${optionClass}">
                                            ${option.innerHTML}
                                        </div>
                                    `;
    }).join('')
    } <
    /div> <
    /div>
    `;
                }).join('')}
            </div>
        </div>
    `;

    modal.innerHTML = reviewHTML;
    document.body.appendChild(modal);
    modal.style.display = 'block';
    }

    function closeReviewModal() {
        const modal = document.querySelector('.review-modal');
        if (modal) {
            modal.remove();
        }
    }

    function toggleExplanation(index) {
        const content = document.getElementById(`explanation-${index}`);
        if (content) {
            content.style.display = content.style.display === 'none' ? 'block' : 'none';
        }
    }


    function submitTest() {
        if (!confirm('Are you sure you want to submit the test?')) {
            return;
        }

        const setId = document.querySelector('#testContainer').dataset.setId;

        fetch('<?= $url('ajax/submit-test') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    mockTestId: setId,
                    answers: userAnswers
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showPerformanceModal({
                        score: data.score,
                        totalMarks: data.totalMarks,
                        correctAnswers: data.correctAnswers,
                        wrongAnswers: data.wrongAnswers,
                        attemptedQuestions: data.attemptedQuestions,
                        totalQuestions: data.totalQuestions,
                        unattemptedQuestions: data.unattemptedQuestions,
                        attemptId: data.attemptId
                    });

                } else {
                    alert('Error submitting test: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function showQuestion(index) {
        // Get all questions
        const questions = document.querySelectorAll('.question-block');
        const targetQuestion = document.getElementById(`question_${index}`);

        if (targetQuestion) {
            // Smooth scroll to target question
            targetQuestion.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            // Update palette active state
            document.querySelectorAll('.palette-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.getElementById(`palette_${index}`).classList.add('active');
        }
    }

    function updatePaletteActive(index) {
        // Remove active class from all palette buttons
        document.querySelectorAll('.palette-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Add active class to current button
        const currentBtn = document.getElementById(`palette_${index}`);
        if (currentBtn) {
            currentBtn.classList.add('active');
        }
    }

    function showNextQuestion() {
        if (currentQuestion < totalQuestions) {
            showQuestion(currentQuestion + 1);
        }
    }

    function showPreviousQuestion() {
        if (currentQuestion > 1) {
            showQuestion(currentQuestion - 1);
        }
    }

    function saveAnswers() {
        const answers = {};
        document.querySelectorAll('input[type="radio"]:checked').forEach(input => {
            const questionId = input.name.split('_')[1];
            answers[questionId] = input.value;
        });

        // Send to server
        fetch('<?= $url('ajax/save-answers') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                answers
            })
        });
    }

    function markAttempted(index) {
        const paletteBtn = document.getElementById(`palette_${index}`);
        if (paletteBtn) {
            paletteBtn.classList.remove('not-visited');
            paletteBtn.classList.add('attempted');
        }
    }

    // Add these functions to mocktest.php
    function markAnswerCorrect(questionId) {
        // Find the question container
        const questionContainer = document.querySelector(`[data-question-id="${questionId}"]`);
        if (questionContainer) {
            // Mark question as correct in palette
            const paletteBtn = document.querySelector(`.question-number-btn[data-question="${questionId}"]`);
            if (paletteBtn) {
                paletteBtn.classList.add('correct');
                paletteBtn.classList.add('attempted');
            }

            // Visual feedback for correct answer
            const selectedOption = questionContainer.querySelector('input[type="radio"]:checked');
            if (selectedOption) {
                const optionLabel = selectedOption.parentElement;
                optionLabel.classList.add('correct-answer');
            }
        }
    }

    function markAnswerWrong(questionId) {
        // Find the question container
        const questionContainer = document.querySelector(`[data-question-id="${questionId}"]`);
        if (questionContainer) {
            // Mark question as wrong in palette
            const paletteBtn = document.querySelector(`.question-number-btn[data-question="${questionId}"]`);
            if (paletteBtn) {
                paletteBtn.classList.add('wrong');
                paletteBtn.classList.add('attempted');
            }

            // Visual feedback for wrong answer
            const selectedOption = questionContainer.querySelector('input[type="radio"]:checked');
            if (selectedOption) {
                const optionLabel = selectedOption.parentElement;
                optionLabel.classList.add('wrong-answer');
            }
        }
    }

    function showPerformanceModal(data) {
        const modal = document.createElement('div');
        modal.className = 'performance-modal';
        modal.innerHTML = `
        <div class="modal-content">
            <h2>Test Performance</h2>
            <div class="performance-stats">
                <div class="stat">
                    <span>Score</span>
                    <strong>${data.score}/${data.totalMarks}</strong>
                </div>
                <div class="stat">
                    <span>Correct Answers</span>
                    <strong>${data.correctAnswers}</strong>
                </div>
                <div class="stat">
                    <span>Wrong Answers</span>
                    <strong>${data.wrongAnswers}</strong>
                </div>
                <div class="stat">
                    <span>Attempted</span>
                    <strong>${data.attemptedQuestions}/${data.totalQuestions}</strong>
                </div>
                <div class="stat">
                    <span>Unattempted</span>
                    <strong>${data.unattemptedQuestions}</strong>
                </div>
            </div>
            <div class="action-buttons">
                <button onclick="showReviewModal()" class="review-btn">
                    Review Answers
                </button>
                <button onclick="window.location = '<?= $url('test') ?>'" class="dashboard-btn">
                    Back to Mocktest
                </button>
            </div>
        </div>
    `;
        document.body.appendChild(modal);
    }

    // ...existing code...
    function playAgain() {
        const pathArray = window.location.pathname.split('/');
        const slug = pathArray[pathArray.length - 1];

        // First clear the session
        const mocktestId = document.getElementById('testContainer').dataset.mocktestId;

        fetch(`/ajax/clear-test-session/${mocktestId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Then redirect to restart the test
                    window.location.href = `/mocktest/restart/${slug}`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Redirect anyway as fallback
                window.location.href = `/mocktest/restart/${slug}`;
            });
    }
    document.addEventListener('DOMContentLoaded', function() {
        showQuestion(1);

        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') showNextQuestion();
            if (e.key === 'ArrowLeft') showPreviousQuestion();
        });
    });
    // Add this JavaScript code
    document.addEventListener('DOMContentLoaded', function() {
        // Close modal when clicking the close button
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
    document.addEventListener('DOMContentLoaded', function() {

        // Show first 5 questions initially
        const questions = document.querySelectorAll('.question-block');
        questions.forEach((q, index) => {
            if (index < 5) {
                q.style.display = 'block';
            }
        });
    });
    setInterval(saveAnswers, 60000);
</script>
