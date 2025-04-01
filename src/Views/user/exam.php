<div class="container exam-container">
    <div class="exam-header">
        <h2><?= htmlspecialchars($exam['title']) ?></h2>
        <div class="exam-status-bar">
            <div id="exam-status">Waiting for exam to start...</div>
            <div id="timer" class="ml-auto">--:--:--</div>
        </div>
    </div>

    <!-- Waiting screen -->
    <div id="waiting-screen" class="text-center py-5">
        <h3>Please wait for the exam to begin</h3>
        <p>The exam will start automatically at the scheduled time.</p>
        <div id="countdown" class="countdown-timer">
            <div class="time-segment">
                <span id="hours">00</span>
                <div class="time-label">Hours</div>
            </div>
            <div class="time-segment">
                <span id="minutes">00</span>
                <div class="time-label">Minutes</div>
            </div>
            <div class="time-segment">
                <span id="seconds">00</span>
                <div class="time-label">Seconds</div>
            </div>
        </div>
        <div class="anti-cheat-notice mt-4">
            <i class="fas fa-shield-alt"></i>
            <p>The exam includes anti-cheating measures. Please ensure you:</p>
            <ul class="text-left">
                <li>Stay on this page for the duration of the exam</li>
                <li>Do not open other tabs or applications</li>
                <li>Have a stable internet connection</li>
                <li>Complete your exam before the time expires</li>
            </ul>
        </div>
    </div>

    <!-- Exam content -->
    <div id="exam-content" style="display: none;">
        <div class="row">
            <div class="col-md-3">
                <!-- Question navigation -->
                <div class="question-palette card sticky-top">
                    <div class="card-header">
                        <h5>Question Palette</h5>
                    </div>
                    <div class="card-body">
                        <div class="question-numbers">
                            <?php foreach ($questions as $index => $question): ?>
                                <button class="question-number-btn"
                                    data-question="<?= $index + 1 ?>"
                                    id="palette-btn-<?= $index + 1 ?>">
                                    <?= $index + 1 ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <div class="palette-legend mt-3">
                            <div class="legend-item"><span class="circle not-visited"></span> Not Visited</div>
                            <div class="legend-item"><span class="circle current"></span> Current</div>
                            <div class="legend-item"><span class="circle answered"></span> Answered</div>
                            <div class="legend-item"><span class="circle marked-review"></span> Marked for Review</div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button id="submit-exam" class="danger btn-block">Submit Exam</button>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <!-- Questions display -->
                <div class="questions-container">
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="question card mb-4" id="question-<?= $index + 1 ?>" style="display: none;">
                            <div class="card-header d-flex">
                                <h5 class="mb-0">Question <?= $index + 1 ?></h5>
                                <div class="ml-auto question-actions">
                                    <button class="btn btn-primary mark-review mb-5"
                                        data-question="<?= $index + 1 ?>">
                                        <i class="far fa-flag"></i> Mark for Review
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="question-text">
                                    <?= htmlspecialchars($question['question_text']) ?>
                                </div>

                                <div class="options mt-4">
                                    <?php foreach ($question['answers'] as $answerIndex => $answer): ?>
                                        <div class="option">
                                            <label class="option-label">
                                                <input type="radio"
                                                    name="question-<?= $question['id'] ?>"
                                                    value="<?= $answer['id'] ?>"
                                                    data-question-id="<?= $question['id'] ?>">
                                                <span class="option-text"><?= htmlspecialchars($answer['answer']) ?></span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="mt-4 d-flex justify-content-between">
                                    <button class="secondary prev-question" <?= $index === 0 ? 'disabled' : '' ?>>
                                        <i class="fas fa-arrow-left"></i> Previous
                                    </button>
                                    <button class="btn primary next-question" <?= $index === count($questions) - 1 ? 'disabled' : '' ?>>
                                        Next <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Anti-cheating scripts and AJAX monitoring -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const examId = <?= $exam['id'] ?>;
        const userId = <?= $_SESSION['user_id'] ?>;
        const totalQuestions = <?= count($questions) ?>;

        let currentQuestion = 1;
        let examInProgress = false;
        let examEnded = false;
        let lastFocusTime = Date.now();
        let warningCount = 0;
        let serverEndTime = null;
        let countdownInterval = null;

        const userAnswers = {};
        const questionStatus = {};

        // Initialize question status
        for (let i = 1; i <= totalQuestions; i++) {
            questionStatus[i] = 'not-visited';
        }

        // Anti-cheating: Monitor tab visibility
        document.addEventListener('visibilitychange', function() {
            if (examInProgress && document.visibilityState === 'hidden') {
                lastFocusTime = Date.now();
            } else if (examInProgress && document.visibilityState === 'visible') {
                const timeAway = Math.floor((Date.now() - lastFocusTime) / 1000);
                if (timeAway > 5) {
                    warningCount++;
                    logSuspiciousActivity('Tab switch detected', {
                        timeAway
                    });

                    if (warningCount >= 3) {
                        alert('WARNING: Excessive tab switching detected. This activity is logged and may be flagged for review.');
                    }
                }
            }
        });

        // Anti-cheating: Disable right-click
        document.addEventListener('contextmenu', function(e) {
            if (examInProgress) {
                e.preventDefault();
                return false;
            }
        });

        // Anti-cheating: Disable copy-paste
        document.addEventListener('copy', function(e) {
            if (examInProgress) {
                e.preventDefault();
                return false;
            }
        });

        document.addEventListener('paste', function(e) {
            if (examInProgress) {
                e.preventDefault();
                return false;
            }
        });

        // AJAX polling for exam status
        function pollExamStatus() {
            fetch(`/api/exam/${examId}/status`)
                .then(response => response.json())
                .then(data => {
                    switch (data.status) {
                        case 'waiting':
                            handleWaitingState(data);
                            break;
                        case 'in_progress':
                            handleExamStarted(data);
                            break;
                        case 'ended':
                            handleExamEnded();
                            break;
                    }
                })
                .catch(error => console.error('Error polling exam status:', error));
        }

        // Poll every 5 seconds
        setInterval(pollExamStatus, 5000);
        // Initial poll on page load
        pollExamStatus();

        function handleWaitingState(data) {
            document.getElementById('waiting-screen').style.display = 'block';
            document.getElementById('exam-content').style.display = 'none';
            document.getElementById('exam-status').textContent = 'Waiting for exam to start';

            // Calculate countdown to start time
            if (data.start_time) {
                const startTime = new Date(data.start_time).getTime();
                const now = Date.now();
                const timeUntilStart = Math.max(0, startTime - now);

                updateCountdown(timeUntilStart);
            }
        }

        function handleExamStarted(data) {
            // Hide waiting screen, show exam
            document.getElementById('waiting-screen').style.display = 'none';
            document.getElementById('exam-content').style.display = 'block';
            document.getElementById('exam-status').textContent = 'Exam in progress';

            examInProgress = true;

            // Calculate time remaining
            if (data.end_time) {
                serverEndTime = new Date(data.end_time).getTime();
                startExamTimer();
            }

            // Show first question if we just started
            if (questionStatus[1] === 'not-visited') {
                showQuestion(1);
            }
        }

        function handleExamEnded() {
            examEnded = true;
            examInProgress = false;

            if (countdownInterval) {
                clearInterval(countdownInterval);
            }

            document.getElementById('timer').textContent = '00:00:00';
            document.getElementById('exam-status').textContent = 'Exam ended';

            // Force submission
            submitExam(true);
        }

        function updateCountdown(timeRemaining) {
            const hours = Math.floor(timeRemaining / (1000 * 60 * 60));
            const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

            document.getElementById('hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
        }

        function startExamTimer() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }

            countdownInterval = setInterval(function() {
                const now = Date.now();
                const timeRemaining = Math.max(0, serverEndTime - now);

                if (timeRemaining <= 0) {
                    clearInterval(countdownInterval);
                    handleExamEnded();
                } else {
                    updateExamTimer(timeRemaining);
                }
            }, 1000);
        }

        function updateExamTimer(timeRemaining) {
            const hours = Math.floor(timeRemaining / (1000 * 60 * 60));
            const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

            const timerElement = document.getElementById('timer');
            timerElement.textContent =
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');

            // Add warning class when less than 5 minutes remaining
            if (timeRemaining < 5 * 60 * 1000) {
                timerElement.classList.add('timer-warning');
            }
        }

        function showQuestion(questionNumber) {
            // Hide all questions
            document.querySelectorAll('.question').forEach(q => {
                q.style.display = 'none';
            });

            // Show the requested question
            const questionElement = document.getElementById(`question-${questionNumber}`);
            if (questionElement) {
                questionElement.style.display = 'block';
            }

            // Update current question
            currentQuestion = questionNumber;

            // Update palette
            updateQuestionPalette();

            // Mark as visited if it was "not-visited"
            if (questionStatus[questionNumber] === 'not-visited') {
                questionStatus[questionNumber] = 'current';
            }
        }

        function updateQuestionPalette() {
            // Reset all buttons
            document.querySelectorAll('.question-number-btn').forEach(btn => {
                btn.className = 'question-number-btn';
                const qNum = parseInt(btn.dataset.question);

                if (questionStatus[qNum]) {
                    btn.classList.add(questionStatus[qNum]);
                }

                if (qNum === currentQuestion) {
                    btn.classList.add('current');
                }
            });
        }

        function submitExam(isForced = false) {
            if (isForced) {
                submitAnswers();
                return;
            }

            // Confirmation dialog
            if (confirm('Are you sure you want to submit your exam? This action cannot be undone.')) {
                submitAnswers();
            }
        }

        function submitAnswers() {
            fetch('/api/exam/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        exam_id: examId,
                        answers: userAnswers
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Exam Submitted!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'Go to Dashboard'
                        }).then(() => {
                            window.location.href = data.redirect_url;
                        });
                    } else {
                        // Handle error
                        Swal.fire({
                            title: 'Error',
                            text: data.error || 'An error occurred while submitting your exam',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to submit exam. Please try again.',
                        icon: 'error'
                    });
                });
        }

        function logSuspiciousActivity(type, details) {
            fetch('/api/exam/log-activity', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    exam_id: examId,
                    user_id: userId,
                    activity_type: type,
                    details: details
                })
            }).catch(error => console.error('Error logging activity:', error));
        }

        // Event listeners for question navigation
        document.querySelectorAll('.prev-question').forEach(btn => {
            btn.addEventListener('click', function() {
                if (currentQuestion > 1) {
                    showQuestion(currentQuestion - 1);
                }
            });
        });

        document.querySelectorAll('.next-question').forEach(btn => {
            btn.addEventListener('click', function() {
                if (currentQuestion < totalQuestions) {
                    showQuestion(currentQuestion + 1);
                }
            });
        });

        // Question palette navigation
        document.querySelectorAll('.question-number-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const questionNumber = parseInt(this.dataset.question);
                showQuestion(questionNumber);
            });
        });

        // Mark for review buttons
        document.querySelectorAll('.mark-review').forEach(btn => {
            btn.addEventListener('click', function() {
                const questionNumber = parseInt(this.dataset.question);
                questionStatus[questionNumber] = 'marked-review';
                updateQuestionPalette();
            });
        });

        // Radio button change (answer selection)
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const questionId = this.dataset.questionId;
                const answerId = this.value;
                const questionNumber = parseInt(this.closest('.question').id.replace('question-', ''));

                // Store the answer
                userAnswers[questionId] = answerId;

                // Update question status
                questionStatus[questionNumber] = 'answered';

                // Update palette
                updateQuestionPalette();
            });
        });

        // Submit button
        document.getElementById('submit-exam').addEventListener('click', function() {
            submitExam();
        });

        // Detect browser close or refresh attempt
        window.addEventListener('beforeunload', function(e) {
            if (examInProgress && !examEnded) {
                const message = 'Leaving this page will end your exam session. Are you sure you want to leave?';
                e.returnValue = message;
                return message;
            }
        });
    });
</script>

<style>
    .exam-container {
        padding-top: 20px;
        min-height: 100vh;
        width: 95%;
        margin: auto;
    }

    .exam-header {
        position: sticky;
        top: 0;
        background: #fff;
        padding: 10px 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        z-index: 100;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .exam-status-bar {
        display: flex;
        align-items: center;
        margin-left: auto;
    }

    #timer {
        font-size: 1.5rem;
        font-weight: bold;
        margin-left: 20px;
    }

    .timer-warning {
        color: red;
        animation: blink 1s infinite;
    }

    @keyframes blink {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1;
        }
    }

    .countdown-timer {
        display: flex;
        justify-content: center;
        margin: 30px 0;
    }

    .time-segment {
        background: #f8f9fa;
        border-radius: 5px;
        padding: 10px 15px;
        margin: 0 10px;
        min-width: 80px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .time-segment span {
        font-size: 2.5rem;
        font-weight: bold;
        color: #343a40;
    }

    .time-label {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 5px;
    }

    .question-palette {
        background: #f8f9fa;
        border-radius: 5px;
    }

    .question-numbers {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .question-number-btn {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ddd;
        background: #fff;
        cursor: pointer;
    }

    .question-number-btn.not-visited {
        background: #fff;
    }

    .question-number-btn.current {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
    }

    .question-number-btn.answered {
        background: #28a745;
        color: #fff;
        border-color: #28a745;
    }

    .question-number-btn.marked-review {
        background: #ffc107;
        color: #212529;
        border-color: #ffc107;
    }

    .palette-legend {
        font-size: 0.8rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .circle {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .circle.not-visited {
        background: #fff;
        border: 1px solid #ddd;
    }

    .circle.current {
        background: #007bff;
    }

    .circle.answered {
        background: #28a745;
    }

    .circle.marked-review {
        background: #ffc107;
    }

    .anti-cheat-notice {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
        padding: 15px;
        max-width: 500px;
        margin: 20px auto;
        color: #721c24;
    }

    .mark-review {
        background: #ffc107;
        color: #212529;
        border-color: #ffc107;
    }

    .option-label {
        display: flex;
        align-items: flex-start;
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .option-label:hover {
        background: #e9ecef;
    }

    .option-text {
        margin-left: 10px;
    }
</style>