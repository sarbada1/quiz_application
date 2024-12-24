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
    <div id="quizModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" data-modal="quizModal">&times;</span>
            <p>Please log in to start the quiz.</p>
            <button class="bg-primary text-dark text-lg" id="startQuiz">Login</button>
        </div>
    </div>
    <?php include __DIR__ . '/auth/login.php'; ?>
    <?php include __DIR__ . '/auth/register.php'; ?>
    <?php if ($isLoggedIn): ?>
        <div class="confirmation-dialog" id="confirmDialog">
            <h2>Exam will be started.</h2>
            <div class="dialog-buttons">
                <button class="btn btn-back" onclick="window.history.back()">Back</button>
                <button class="btn btn-confirm" onclick="startTest()">Confirm</button>
            </div>
        </div>
        <div class="test-container" id="testContainer" data-mocktest-id="<?= $mockTest['id'] ?>">
            <div class="header">
                <div class="timer-section">
                    <div class="timer" id="timer">
                        <i class="fas fa-clock"></i>
                        <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
                    </div>
                    <div class="timer-progress" id="timerProgress"></div>
                </div>
                <button onclick="showRules()" class="btn btn-rules">
                    <i class="fas fa-info-circle"></i> Show Rules
                </button>
                <div class="control-buttons">
                    <button class="btn btn-submit" onclick="submitTest()">
                        <i class="fas fa-check-circle"></i> Submit
                    </button>
                </div>
            </div>

            <div class="main-content">
                <div class="question-container" id="questionContainer">
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="question mt-5" data-question-id="<?= $question['id'] ?>" id="question-<?= $question['id'] ?>">
                            <div class="question-text">
                                <?= ($index + 1) . ". " . htmlspecialchars($question['question_text']) ?>
                                <button class="btn-report" onclick="reportQuestion(<?= $question['id'] ?>)">
                                    <i class="fas fa-flag"></i> Report Question
                                </button>
                            </div>

                            <div class="options mt-3">
                                <?php foreach ($question['answers'] as $answer): ?>
                                    <label class="option">
                                        <input type="radio" name="question_<?= $question['id'] ?>" onclick="submitAnswer(this.value,<?= $question['id'] ?>)" value="<?= $answer['id'] ?>">
                                        <?= htmlspecialchars($answer['answer']) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="question-palette">
                    <div class="palette-title">Question Palette</div>
                    <div class="question-numbers" id="questionPalette">
                        <?php foreach ($questions as $index => $question): ?>
                            <div class="question-number-btn" onclick="jumpToQuestion(<?= $index ?>)">
                                <?= $index + 1 ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="legend">
                        <div class="legend-item">
                            <div class="legend-color attempted"></div>
                            <span>Attempted</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color unattempted"></div>
                            <span>Unattempted</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <script>
            // Show login modal for unauthorized users
            document.getElementById('quizModal').style.display = 'block';

            // Prevent access to test content
            if (document.getElementById('testContainer')) {
                document.getElementById('testContainer').style.display = 'none';
            }
        </script>
    <?php endif; ?>


    <div id="reportQuestionModal" class="modal">
        <div class="modal-content">
            <span class="close" data-modal="reportQuestionModal">&times;</span>
            <h2>Report Question</h2>
            <form id="reportForm" method="POST" action="/question/report">
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
</body>
<script>
    let currentQuestionIndex = 0;
    const questionsPerPage = 1; // Show one question at a time

    function showRules() {
        const rules = `
        <div class="test-rules">
            <h3>Mock Test Rules & Information</h3>
            <ul>
                <li>Total duration: ${duration} minutes</li>
                <li>Each question has only one correct answer</li>
                <li>Once you confirm an answer, it cannot be changed</li>
                <li>Green indicates correct answer, red indicates wrong answer</li>
                <li>Test will auto-submit when time expires</li>
                <li>You can submit the test anytime using the Submit button</li>
            </ul>
        </div>
    `;
        document.querySelector('.test-container').insertAdjacentHTML('afterbegin', rules);
    }

    function startTest() {
        console.log('startTest function called'); // Debug log

        try {
            const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
            console.log('Login status:', isLoggedIn);

            if (!isLoggedIn) {
                document.getElementById('quizModal').style.display = 'block';
                return;
            }

            const confirmDialog = document.getElementById('confirmDialog');
            const testContainer = document.getElementById('testContainer');

            console.log('confirmDialog:', confirmDialog); // Debug log
            console.log('testContainer:', testContainer); // Debug log

            if (confirmDialog) confirmDialog.style.display = 'none';
            if (testContainer) testContainer.style.display = 'block';

            startTimer();
            showQuestion(currentQuestion);
            initializeQuestionPalette();

        } catch (error) {
            console.error('Error in startTest:', error);
        }
    }

    // Add this JavaScript to handle the timer
    function startTimer(duration) {
        let timer = duration;
        let hours, minutes, seconds;

        const countdown = setInterval(function() {
            hours = parseInt(timer / 3600, 10);
            minutes = parseInt((timer % 3600) / 60, 10);
            seconds = parseInt(timer % 60, 10);

            hours = hours < 10 ? "0" + hours : hours;
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            document.getElementById('hours').textContent = hours;
            document.getElementById('minutes').textContent = minutes;
            document.getElementById('seconds').textContent = seconds;

            // Update progress bar
            const progress = (timer / duration) * 100;
            document.getElementById('timerProgress').style.width = progress + '%';

            // Warning colors
            if (timer < 300) { // Last 5 minutes
                document.getElementById('timer').classList.add('timer-warning');
            }
            if (timer < 60) { // Last minute
                document.getElementById('timer').classList.add('timer-danger');
            }

            if (--timer < 0) {
                clearInterval(countdown);
                alert("Time's up! Your test will be submitted automatically.");
                showPerformanceModal();
                submitTest();
            }
        }, 1000);
    }
    function showQuestion(index) {
    const questions = document.querySelectorAll('.question');
    const totalQuestions = questions.length;
    
    // Validate index
    if (index < 0 || index >= totalQuestions) return;
    
    // Hide all questions
    questions.forEach(q => q.style.display = 'none');
    
    // Show selected question
    questions[index].style.display = 'block';
    currentQuestionIndex = index;
    
    // Update palette highlighting
    updatePaletteHighlight(index);
    
    // Update pagination buttons
    document.getElementById('prevQuestion').disabled = index === 0;
    document.getElementById('nextQuestion').disabled = index === totalQuestions - 1;
}

    function selectAnswer(questionId, selectedOption) {
    if (confirmedAnswers.includes(questionId)) {
        alert("You've already confirmed an answer for this question!");
        return;
    }
    function updatePaletteHighlight(currentIndex) {
    // Remove current highlight
    document.querySelectorAll('.question-number-btn').forEach(btn => {
        btn.classList.remove('current');
    });
    
    // Add highlight to current question
    const currentBtn = document.querySelector(`.question-number-btn[data-index="${currentIndex}"]`);
    if (currentBtn) {
        currentBtn.classList.add('current');
    }
}
    // Show confirmation dialog
    const confirmSubmit = confirm("Are you sure you want to submit this answer? You cannot change it after confirmation.");
    
    if (confirmSubmit) {
        const questionElement = document.querySelector(`[data-question-id="${questionId}"]`);
        const options = questionElement.querySelectorAll('.option');
        
        // Disable all radio buttons for this question
        const radioButtons = questionElement.querySelectorAll('input[type="radio"]');
        radioButtons.forEach(radio => radio.disabled = true);
        
        confirmedAnswers.push(questionId);
        
        // Call API to submit answer
        fetch(`/ajax/submit-answer/${selectedOption}/${questionId}/${mockTestId}`)
            .then(response => response.json())
            .then(data => {
                if (data.isCorrect) {
                    markAnswerCorrect(questionId);
                } else {let currentQuestionIndex = 0;

                    markAnswerWrong(questionId);
                }
                
                // Update question palette
                updateQuestionPalette(questionId, data.isCorrect);
            })
            .catch(error => console.error('Error:', error));
    }
}

function updateQuestionPalette(questionId, isCorrect) {
    const paletteBtn = document.querySelector(`.question-number-btn[data-question="${questionId}"]`);
    if (paletteBtn) {
        paletteBtn.classList.add('attempted');
        paletteBtn.classList.add(isCorrect ? 'correct' : 'wrong');
    }
}

function initializeQuestionPalette() {
    const palette = document.createElement('div');
    palette.className = 'question-palette';
    
    // Get all questions
    const questions = document.querySelectorAll('.question');
    
    // Create number buttons
    questions.forEach((_, index) => {
        const numberBtn = document.createElement('button');
        numberBtn.className = 'question-number-btn';
        numberBtn.textContent = index + 1;
        numberBtn.setAttribute('data-index', index);
        numberBtn.onclick = () => navigateToQuestion(index);
        palette.appendChild(numberBtn);
    });

    // Add pagination controls
    const paginationControls = `
        <div class="pagination-controls">
            <button id="prevQuestion" onclick="navigateToQuestion(currentQuestionIndex - 1)">
                <i class="fas fa-arrow-left"></i>
            </button>
            <button id="nextQuestion" onclick="navigateToQuestion(currentQuestionIndex + 1)">
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    `;
    function navigateToQuestion(index) {
    showQuestion(index);
}
    // Add palette to DOM
    document.querySelector('.test-container').insertAdjacentElement('afterbegin', palette);
    document.querySelector('.test-container').insertAdjacentHTML('beforeend', paginationControls);
    
    // Show initial question
    showQuestion(0);
}
</script>
<script>
    let answeredQuestions = new Set();
    let currentQuestion = 0;
    let timer;

    document.addEventListener('DOMContentLoaded', function() {
        const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

        if (!isLoggedIn) {
            document.getElementById('quizModal').style.display = 'block';
        }

        // Add start test button handler
        document.getElementById('startTestBtn').addEventListener('click', startTest);

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
        loadSavedProgress();

    });



    function reportQuestion(questionId) {
        const modal = document.getElementById('reportQuestionModal');
        const questionIdInput = document.getElementById('reportQuestionId');
        if (modal && questionIdInput) {
            questionIdInput.value = questionId;
            modal.style.display = 'block';
        }
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
        if (answeredQuestions.has(questionId)) {
            return;
        }

        const mocktestId = document.getElementById('testContainer').dataset.mocktestId;

        fetch(`/ajax/submit-answer/${answerId}/${questionId}/${mocktestId}`, {
                method: 'GET',
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                answeredQuestions.add(questionId);

                const questionContainer = document.querySelector(`[data-question-id="${questionId}"]`);
                const allOptions = questionContainer.querySelectorAll('input[type="radio"]');
                allOptions.forEach(option => {
                    option.disabled = true;
                });

                const allOptionLabels = questionContainer.querySelectorAll('.option');
                allOptionLabels.forEach(label => {
                    label.classList.add('disabled');
                });

                // Save progress after successful submission
                saveProgress(answerId, questionId, data.isCorrect);

                if (data.isCorrect) {
                    markAnswerCorrect(questionId);
                } else {
                    markAnswerWrong(questionId);
                }
                markAttempted(currentQuestion);
            })
            .catch(error => console.error('Error:', error));
    }




    function submitTest() {
        clearInterval(timer);

        if (confirm('Are you sure you want to submit the test?')) {
            const mocktestId = document.getElementById('testContainer').dataset.mocktestId;
            const timeTaken = document.getElementById('timer').innerText;

            fetch('/ajax/submit-performance', {
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

    let savedProgress = {
        answers: new Map(),
        currentQuestion: 0,
        timeLeft: null
    };

    function saveProgress(answerId, questionId, isCorrect) {
        savedProgress.answers.set(questionId, {
            answerId: answerId,
            isCorrect: isCorrect
        });
        savedProgress.currentQuestion = currentQuestion;
        savedProgress.timeLeft = timeLeft;

        // Save to localStorage
        localStorage.setItem(`mocktest_${mocktestId}`, JSON.stringify({
            answers: Array.from(savedProgress.answers.entries()),
            currentQuestion: currentQuestion,
            timeLeft: timeLeft
        }));

        // Save to server
        fetch('/ajax/save-progress', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                mockTestId: mocktestId,
                answers: Array.from(savedProgress.answers.entries()),
                currentQuestion: currentQuestion,
                timeLeft: timeLeft
            })
        });
    }

    function loadSavedProgress() {
        const mocktestId = document.getElementById('testContainer').dataset.mocktestId;
        const saved = localStorage.getItem(`mocktest_${mocktestId}`);

        if (saved) {
            const data = JSON.parse(saved);

            // Restore answers
            savedProgress.answers = new Map(data.answers);
            savedProgress.currentQuestion = data.currentQuestion;
            savedProgress.timeLeft = data.timeLeft;

            // Apply saved answers
            savedProgress.answers.forEach((answer, questionId) => {
                const input = document.querySelector(`input[name="question_${questionId}"][value="${answer.answerId}"]`);
                if (input) {
                    input.checked = true;
                    input.disabled = true;
                    answeredQuestions.add(parseInt(questionId));

                    const questionContainer = input.closest('.question');
                    if (questionContainer) {
                        const allOptions = questionContainer.querySelectorAll('.option');
                        allOptions.forEach(option => option.classList.add('disabled'));
                    }
                }
            });

            // Restore question position
            currentQuestion = data.currentQuestion;
            showQuestion(currentQuestion);

            // Restore timer
            if (data.timeLeft) {
                timeLeft = data.timeLeft;
            }
        }
    }
</script>