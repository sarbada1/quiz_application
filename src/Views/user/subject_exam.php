<body>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['status'] ?? 'info' ?>">
            <?= $_SESSION['message'] ?>
        </div>
        <?php
        unset($_SESSION['message']);
        unset($_SESSION['status']);
        ?>
    <?php endif; ?>

    <div id="quizModal" class="modal">
        <div class="modal-content">
            <span class="close" data-modal="quizModal">&times;</span>
            <p>Please log in to start the test.</p>
            <button class="bg-primary text-dark text-lg" id="startQuiz">Login</button>
        </div>
    </div>

    <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
        <div class="confirmation-dialog" id="confirmDialog">
            <h2><?= htmlspecialchars($subject['name']) ?> Test</h2>
            <div class="dialog-buttons">
                <button class="btn btn-back" onclick="window.history.back()">Back</button>
                <button class="btn btn-confirm" onclick="startTest()">Start Test</button>
            </div>
        </div>

        <div class="test-container" id="testContainer" data-subject-id="<?= $mockTest['id'] ?>">
            <div class="header">
                <div class="timer" id="timer">
                    <i class="fas fa-clock"></i>
                    --:--:--
                </div>
                <div class="control-buttons">
                    <button class="btn btn-submit" onclick="submitTest()">
                        <i class="fas fa-check-circle"></i> Submit
                    </button>
                </div>
            </div>

            <div class="main-content">
                <div class="question-container">
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="question" data-question-id="<?= $question['id'] ?>">
                            <div class="question-text">
                                <?= ($index + 1) . ". " . htmlspecialchars($question['question_text']) ?>
                            </div>
                            <div class="options">
                                <?php foreach ($question['answers'] as $answer): ?>
                                    <label class="option">
                                        <input type="radio" 
                                               name="question_<?= $question['id'] ?>" 
                                               value="<?= $answer['id'] ?>"
                                               onclick="submitAnswer(this.value, <?= $question['id'] ?>)">
                                        <?= htmlspecialchars($answer['answer']) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="question-palette">
                    <div class="palette-title">Question Palette</div>
                    <div class="question-numbers">
                        <?php foreach ($questions as $index => $question): ?>
                            <div class="question-number-btn" onclick="jumpToQuestion(<?= $index ?>)">
                                <?= $index + 1 ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
    <?php include __DIR__ . '/auth/login.php'; ?>
    <?php include __DIR__ . '/auth/register.php'; ?>
<script>
    let answeredQuestions = new Set();
    let currentQuestion = 0;
    let timer;

    document.addEventListener('DOMContentLoaded', function() {
        const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

        if (!isLoggedIn) {
            document.getElementById('quizModal').style.display = 'block';
        }

        // Close modal when clicking the close button
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('quizModal').style.display = 'none';
            window.history.back();
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('quizModal')) {
                document.getElementById('quizModal').style.display = 'none';
                window.history.back();
            }
        }
    });

    function startTest() {
        const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

        if (!isLoggedIn) {
            document.getElementById('quizModal').style.display = 'block';
            return;
        }

        document.getElementById('confirmDialog').style.display = 'none';
        document.getElementById('testContainer').style.display = 'block';
        startTimer();
        showQuestion(currentQuestion);
        initializeQuestionPalette();
    }

    function reportQuestion(questionId) {
        const modal = document.getElementById('reportQuestionModal');
        const questionIdInput = document.getElementById('reportQuestionId');
        if (modal && questionIdInput) {
            questionIdInput.value = questionId;
            modal.style.display = 'block';
        }
    }

    function showQuestion(index) {
        // Hide all questions
        const allQuestions = document.querySelectorAll('.question');
        allQuestions.forEach((q) => {
            // q.style.display = 'none';
        });

        // Show the current question
        const current = allQuestions[index];
        if (current) {
            current.style.display = 'block';
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

    function markAttempted(index) {
        const paletteButtons = document.querySelectorAll('.question-number-btn');
        paletteButtons[index].classList.add('attempted');
    }

    function jumpToQuestion(index) {
        currentQuestion = index;
        showQuestion(currentQuestion);
    }

    let timeLeft = <?= $mockTest['time'] * 60 ?>; // Convert minutes to seconds

    function startTimer() {
        const timerDisplay = document.getElementById('timer');
        timer = setInterval(() => {
            const hours = Math.floor(timeLeft / 3600);
            const minutes = Math.floor((timeLeft % 3600) / 60);
            const seconds = timeLeft % 60;

            timerDisplay.innerHTML = `
                <i class="fas fa-clock"></i>
                ${hours.toString().padStart(2, '0')}:
                ${minutes.toString().padStart(2, '0')}:
                ${seconds.toString().padStart(2, '0')}
            `;

            if (timeLeft <= 300) { // 5 minutes remaining
                timerDisplay.style.color = '#e74c3c';
                timerDisplay.style.animation = 'pulse 1s infinite';
            }

            if (timeLeft <= 0) {
                clearInterval(timer);
                submitTest();
            }

            timeLeft--;
        }, 1000);
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

    function saveAnswers() {
        alert('Answers saved successfully!');
    }





    // Update submitAnswer function
    function submitAnswer(answerId, questionId) {
        // Check if question already answered
        if (!confirm('Are you sure you want to submit this answer? You won\'t be able to change it later.')) {
            // If user cancels, uncheck the radio button
            const selectedOption = document.querySelector(`input[type="radio"][value="${answerId}"]`);
            if (selectedOption) {
                selectedOption.checked = false;
            }
            return;
        }

        if (answeredQuestions.has(questionId)) {
            return; // Exit if already answered
        }

        const mocktestId = document.getElementById('testContainer').dataset.mocktestId;

        fetch(`/ajax/subject/submit-answer/${answerId}/${questionId}/${mocktestId}`, {
                method: 'GET',
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                // Add question to answered set
                answeredQuestions.add(questionId);

                // Disable all radio buttons for this question
                const questionContainer = document.querySelector(`[data-question-id="${questionId}"]`);
                const allOptions = questionContainer.querySelectorAll('input[type="radio"]');
                allOptions.forEach(option => {
                    option.disabled = true;
                });

                // Add disabled style to options
                const allOptionLabels = questionContainer.querySelectorAll('.option');
                allOptionLabels.forEach(label => {
                    label.classList.add('disabled');
                });

                if (data.isCorrect) {
                    markAnswerCorrect(questionId);
                } else {
                    markAnswerWrong(questionId);
                }
                markAttempted(currentQuestion);
            })
            .catch(error => console.error('Error:', error));
    }


    // Update submitTest function in mocktest.php:

    // Update submitTest function with better error handling
    function submitTest() {
        clearInterval(timer);

        if (confirm('Are you sure you want to submit the test?')) {
            const mocktestId = document.getElementById('testContainer').dataset.mocktestId;
            const timeTaken = document.getElementById('timer').innerText;

            fetch('<?= $url('ajax/subject/submit-performance') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        mockTestId: mocktestId,
                        timeTaken: timeTaken
                    }),
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Server error:', data.error);
                        throw new Error(data.error);
                    }
                    console.log('Performance data:', data);
                    showPerformanceModal(
                        parseInt(data.correctAnswers || 0),
                        parseInt(data.wrongAnswers || 0),
                        parseFloat(data.score || 0),
                        parseInt(data.totalQuestions || 0)
                    );
                })
                .catch(error => {
                    console.error('Submission error:', error);
                    alert(`Failed to submit test: ${error.message}`);
                    startTimer();
                });
        } else {
            startTimer();
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

    function showPerformanceModal(correct, wrong, score, total) {
        const scorePercentage = (score * 360) / 100;
        const modalHtml = `
        <div class="performance-modal">
            <h2>Performance Summary</h2>
            <div class="score-circle" style="--score: ${scorePercentage}deg">
                <div class="score-inner">${score.toFixed(1)}%</div>
            </div>
            <div class="stats-container">
                <div class="stat-item">
                    <div class="stat-label">Total Questions</div>
                    <div class="stat-value">${total}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Correct Answers</div>
                    <div class="stat-value" style="color: #2ecc71">${correct}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Wrong Answers</div>
                    <div class="stat-value" style="color: #e74c3c">${wrong}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Unattempted</div>
                    <div class="stat-value" style="color: #7f8c8d">${total - (correct + wrong)}</div>
                </div>
            </div>
            <div class="buttons">
                <button class="play-again bg-success" onclick="playAgain()">Play Again</button>
                <button class="close-modal" onclick="window.history.back()">Close</button>
            </div>
        </div>
    `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
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
</script>