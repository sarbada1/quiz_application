<style>
    /* Add to style.css */
    .quiz-play-container {
        max-width: 800px;
        margin: 100px auto;
        padding: 20px;
    }

    .quiz-header {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .quiz-info {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
    }

    .question-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .score-circle {
        width: 150px;
        height: 150px;
        margin: 20px auto;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quiz-controls {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        gap: 10px;
    }

    .quiz-controls button {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .quiz-controls button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    #submitBtn {
        display: none;
        background-color: red;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }


    .results-container {
        width: 800px;
        margin: 100px 425px;
        padding: 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .score-section {
        display: flex;
        align-items: center;
        justify-content: space-around;
        margin: 2rem 0;
    }

    .score-circle {
        width: 200px;
        height: 200px;
        position: relative;
        background: conic-gradient(#4CAF50 var(--score), #f0f0f0 0deg);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .score-value {
        font-size: 2.5rem;
        font-weight: bold;
        color: #2c3e50;
    }

    .score-stats {
        display: grid;
        gap: 1rem;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.2rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin: 2rem 0;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card h3 {
        color: #2d3748;
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .stat-card .value {
        font-size: 2rem;
        font-weight: bold;
        color: #4299e1;
    }

    .score-section {
        background: linear-gradient(135deg, #4299e1, #3182ce);
        padding: 2rem;
        border-radius: 15px;
        color: white;
        margin: 2rem 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .score-value {
        font-size: 3rem;
        font-weight: bold;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #4299e1;
        color: white;
        border: none;
    }

    .btn-secondary {
        background: white;
        color: #4299e1;
        border: 2px solid #4299e1;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(66, 153, 225, 0.2);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .score-section {
        display: flex;
        align-items: center;
        justify-content: space-around;
        margin: 2rem 0;
    }

    .score-circle {
        width: 200px;
        height: 200px;
        position: relative;
        /* background: conic-gradient(#4CAF50 var(--score), #f0f0f0 0deg); */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .score-value {
        font-size: 2.5rem;
        font-weight: bold;
        color: #2c3e50;
    }

    .score-stats {
        display: grid;
        gap: 1rem;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.2rem;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }

    .action-buttons button {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .review-btn {
        background: #3498db;
        color: white;
    }

    .retry-btn {
        background: #2ecc71;
        color: white;
    }

    .home-btn {
        background: #95a5a6;
        color: white;
    }

    .answer-review {
        margin-top: 2rem;
    }

    .review-item {
        background: white;
        padding: 1.5rem;
        margin: 1rem 0;
        border-radius: 8px;
        border-left: 4px solid;
    }

    .review-item.correct {
        border-left-color: #2ecc71;
    }

    .review-item.wrong {
        border-left-color: #e74c3c;
    }

    .result-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.9rem;
    }

    .result-badge.correct {
        background: #d4edda;
        color: #155724;
    }

    .result-badge.wrong {
        background: #f8d7da;
        color: #721c24;
    }

    .answer-option {
        padding: 10px;
        margin: 5px 0;
        border-radius: 4px;
    }

    .selected-correct {
        background-color: #7dd15a;
        border: 1px solid #7dd15a;
    }

    .selected-wrong {
        background-color: #ff4545;
        border: 1px solid #ff4545;
    }

    .correct-answer {
        background-color: #7dd15a;
        border: 1px solid #7dd15a;
    }

    .wrong-mark {
        color: #d9534f;
        margin-left: 10px;
    }

    .correct-mark {
        color: #5cb85c;
        margin-left: 10px;
    }

    .explanation-toggle {
        background: transparent;
        border: 1px solid #ddd;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 1rem;
    }

    .explanation-text {
        margin-top: 1rem;
        margin: 130px auto;
        max-width: 1200px;
        background: #f8f9fa;
        border-radius: 4px;
    }

    .review-container {
        width: 800px;
        margin: 0 425px;
        padding: 20px;
    }

    .review-item {
        background: white;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .question-text {
        font-size: 1.1rem;
        margin-bottom: 15px;
    }





    .option.correct {
        /* background-color: #28a745; */
        border: 1px solid #28a745;
    }

    .option.incorrect {
        /* background-color: #dc3545; */
        border: 1px solid #dc3545;
    }

    .option.selected-correct {
        /* background-color: #28a745; */
        border: 2px solid #28a745;
    }

    .option.selected-incorrect {
        /* background-color: #dc3545; */
        border: 2px solid #dc3545;
    }

    .correct-mark,
    .wrong-mark {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: bold;
    }

    .correct-mark {
        color: #155724;
    }

    .wrong-mark {
        color: #721c24;
    }

    .explanation {
        margin-top: 15px;
    }

    .explanation-toggle {
        background: none;
        border: 1px solid #ddd;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
    }

    .explanation-text {
        margin-top: 10px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 4px;
        color: #495057;
    }

    .quiz-layout {
        display: grid;
        grid-template-columns: 250px 1fr 250px;
        gap: 20px;
        /* min-height: 100vh; */
        background: #f5f7fa;
        padding: 20px;
        margin: 130px auto;
        /* width: 100%; */
    }

    .side-panel {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .quiz-main {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .participant-card {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(145deg, #3498db, #2980b9);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar i {
        color: white;
        font-size: 1.5rem;
    }


    .timer-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 4px solid #4299e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 20px auto;
    }


    .option:hover {
        border-color: #4299e1;
        background: #ebf8ff;
    }



    .progress-bar {
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        margin: 20px 0;
    }

    .progress-fill {
        height: 100%;
        background: #4299e1;
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    .feedback-panel {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 30px;
        height: fit-content;
    }

    .question-timer {
        font-size: 2.5rem;
        text-align: center;
        color: #2c3e50;
        font-weight: 700;
        margin: 15px 0;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .question-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
    }





    .quiz-controls {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-top: 30px;
    }



    .answer-feedback {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .feedback-correct {
        color: #28a745;
        font-weight: bold;
    }

    .feedback-wrong {
        color: #dc3545;
        font-weight: bold;
    }


    .quiz-results {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
    }

    .score-container {
        position: relative;
        width: 200px;
        height: 200px;
        margin: 0 auto 2rem;
    }

    .progress-ring-circle {
        transition: stroke-dashoffset 0.8s ease-in-out;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin: 2rem 0;
    }

    .metric-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .metric-card:hover {
        transform: translateY(-5px);
    }

    .question-timeline {
        display: flex;
        gap: 0.5rem;
        margin: 1rem 0;
        overflow-x: auto;
        padding: 1rem 0;
    }

    .result-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }

    .result-actions button {
        padding: 0.8rem 1.5rem;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
    }

    @keyframes slideIn {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>
<div class="quiz-layout" data-attempt-id="<?= $attemptId ?? '' ?>">


        <div class="side-panel">
            <div class="participant-card">
                <div class="avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h3><?= $user['username'] ?></h3>
                    <p>Participant</p>
                </div>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: 0%"></div>
            </div>
        </div>
  
    <div class="quiz-main">
        <?php if (isset($attemptId)): ?>
            <form id="quizForm" data-attempt-id="<?= $attemptId ?>" data-quiz-id="<?= $quiz['id'] ?>">
                <?php foreach ($questions as $index => $question): ?>
                    <div class="question-card" id="q<?= $index ?>" style="display: <?= $index === 0 ? 'block' : 'none' ?>">
                        <h3><?= ($index + 1) . '. ' . ($question['question_text']) ?></h3>
                        <div class="options">
                            <?php foreach ($question['answers'] as $key => $answer): ?>
                                <label class="option">
                                    <input type="radio" name="q_<?= $question['id'] ?>" value="<?= $answer['id'] ?>"
                                        data-correct="<?= $answer['is_correct'] ?>">
                                    <?= ($answer['text']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </form>
        <?php else: ?>
            <div class="error">Error: No attempt ID found. Please try again.</div>
        <?php endif; ?>
    </div>

    <div class="side-panel">
        <h3>Time Remaining</h3>
        <div class="timer-circle">
            <span class="question-timer">30</span>
        </div>
        <div class="answer-feedback">
            <h3>Feedback</h3>
            <div id="feedbackContent"></div>
        </div>
    </div>
</div>

<script>
    const QUESTION_TIME_LIMIT = 30; // seconds per question
    let quizState = {
        answers: {},
        currentIndex: 0,
        totalQuestions: 0,
        timer: null,
        questionTimer: null,
        isSubmitting: false
    };

    let timeRemaining;

    const totalQuestions = <?= count($questions) ?>;
    let answeredQuestions = new Set();
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize quiz state
        initializeQuiz();
    });

    function initializeQuiz() {
        try {
            quizState.totalQuestions = document.querySelectorAll('.question-card').length;

            document.querySelectorAll('.question-card').forEach((card, index) => {
                const questionId = card.dataset.questionId;
                quizState.answers[questionId] = {
                    answerId: null,
                    isCorrect: false,
                    questionOrder: index,
                    timeSpent: 0
                };
            });

            showQuestion(0);
            startTimer();
            updateStatistics();
        } catch (error) {
            console.error('Quiz initialization failed:', error);
            showError('Failed to initialize quiz. Please refresh the page.');
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('quizForm');
        if (form) {
            console.log('Form found with attempt ID:', form.dataset.attemptId);
        } else {
            console.error('Quiz form not found');
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        const timerElement = document.getElementById('timer');
        const questionTimerElement = document.getElementById('questionTimer');

        if (timerElement) {
            startTimer();
        }

        if (questionTimerElement) {
            startQuestionTimer();
        }

        // Initialize first question
        showQuestion(0);
    });

    function updateStatistics() {
        const statsContainer = document.getElementById('quizStats');
        if (!statsContainer) return;

        const stats = calculateQuizStats();
        statsContainer.innerHTML = `
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-label">Questions</span>
                <span class="stat-value">${stats.total}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Answered</span>
                <span class="stat-value">${stats.answered}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Correct</span>
                <span class="stat-value">${stats.correct}</span>
            </div>
        </div>
    `;
    }

    function startTimer() {
        const startTime = Date.now();
        const timerElement = document.getElementById('timer');
        if (!timerElement) return; // Ensure the timer element exists

        quizState.timer = setInterval(() => {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;

            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    }

    function calculateQuizStats() {
        // Implement the function to calculate quiz statistics
        return {
            total: quizState.totalQuestions,
            answered: Object.values(quizState.answers).filter(a => a.answerId !== null).length,
            correct: Object.values(quizState.answers).filter(a => a.isCorrect).length
        };
    }

    function showError(message) {
        // Implement the function to show error messages
        alert(message);
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initial currentIndex:', currentIndex); // Debug log
        startTimer();
        showQuestion(currentIndex);
        updateNavButtons();
    });

    function showQuestion(index) {
        currentIndex = index;

        // Hide all questions and show current one
        document.querySelectorAll('.question-card').forEach((card, i) => {
            card.style.display = i === index ? 'block' : 'none';
        });

        // Reset feedback content
        document.getElementById('feedbackContent').innerHTML = '';
    }


    function startQuestionTimer() {
        timeRemaining = QUESTION_TIME_LIMIT;
        updateTimerDisplay();

        questionTimer = setInterval(() => {
            timeRemaining--;
            updateTimerDisplay();

            if (timeRemaining <= 0) {
                clearInterval(questionTimer);
                handleTimeUp();
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        document.querySelector('.question-timer').textContent = timeRemaining;
        if (timeRemaining <= 10) {
            document.querySelector('.question-timer').style.color = '#dc3545';
        }
    }

    function handleOptionSelect(input) {
        const isCorrect = input.dataset.correct === "1";
        const optionContainer = input.closest('.option');
        const allOptions = input.closest('.options').querySelectorAll('.option');

        // Disable all options
        allOptions.forEach(option => {
            const optionInput = option.querySelector('input');
            optionInput.disabled = true;

            // Show correct/incorrect for all options
            if (optionInput.dataset.correct === "1") {
                option.classList.add('correct');
            }
        });

        // Add selected state
        if (isCorrect) {
            optionContainer.classList.add('selected-correct');
        } else {
            optionContainer.classList.add('selected-incorrect');
            // Show which one was correct
            allOptions.forEach(option => {
                if (option.querySelector('input').dataset.correct === "1") {
                    option.classList.add('correct');
                }
            });
        }
    }

    function handleTimeUp() {
        const currentQuestion = document.getElementById(`q${currentIndex}`);
        const correctAnswer = currentQuestion.querySelector('input[data-correct="1"]');
        const feedback = document.getElementById('feedbackContent');

        // Show correct answer
        correctAnswer.parentElement.classList.add('correct-answer');
        feedback.innerHTML = `
        <div class="feedback-wrong">Time's up!</div>
        <div>The correct answer was: ${correctAnswer.nextSibling.textContent.trim()}</div>
    `;

        // Disable all options
        currentQuestion.querySelectorAll('input[type="radio"]').forEach(input => {
            input.disabled = true;
        });

        // Auto advance to next question after 3 seconds
        setTimeout(() => {
            if (currentIndex < totalQuestions - 1) {
                nextQuestion();
            }
        }, 3000);
    }

    function updateNavButtons() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn'); // Fixed typo
        const submitBtn = document.getElementById('submitBtn');

        // Check if elements exist before using them
        if (prevBtn) {
            prevBtn.style.display = currentIndex === 0 ? 'none' : 'block';
        }

        if (nextBtn && submitBtn) {
            if (currentIndex >= totalQuestions - 1) {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'block';
            } else {
                nextBtn.style.display = 'block';
                submitBtn.style.display = 'none';
            }
        }
    }


    function prevQuestion() {
        console.log('Before prev - currentIndex:', currentIndex); // Debug log
        if (currentIndex > 0) {
            currentIndex--;
            console.log('After prev - currentIndex:', currentIndex); // Debug log
            showQuestion(currentIndex);
        }
    }

    function nextQuestion() {
        if (currentIndex < totalQuestions - 1) {
            currentIndex++;
            showQuestion(currentIndex);
            clearInterval(questionTimer);
            startQuestionTimer();
        } else {
            submitQuiz();
        }
    }

    function calculateTimeSpent() {
        const startTime = quizState.startTime || Date.now();
        return Math.floor((Date.now() - startTime) / 1000);
    }


    function submitQuiz() {
    const form = document.getElementById('quizForm');
    const attemptId = form.dataset.attemptId;
    const quizId = form.dataset.quizId;
  

    if (!attemptId) {
        console.error('No attempt ID found');
        return;
    }

    const answers = [];
    let correctCount = 0;
    let wrongCount = 0;

    // Gather all answers
    document.querySelectorAll('.question-card').forEach((card, index) => {
        const selectedInput = card.querySelector('input[type="radio"]:checked');
        const questionId = card.querySelector('input[type="radio"]').name.split('_')[1];
     
    
        if (selectedInput) {
            const isCorrect = selectedInput.dataset.correct === "1";
            answers.push({
                questionId: questionId,
                answerId: selectedInput.value,
                isCorrect: isCorrect,
                questionOrder: index
            });

            if (isCorrect) correctCount++;
            else wrongCount++;
        }
    });

    // Send to server
    fetch('/quiz/submit', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            attemptId: attemptId,
            quizId: quizId,
            answers: answers,
            correctCount: correctCount,
            wrongCount: wrongCount,
            totalQuestions: totalQuestions,
            score: (correctCount / totalQuestions) * 100
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        
        if (data.success) {
            showResults(data);
        } else {
            throw new Error(data.error || 'Failed to submit quiz');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to submit quiz: ' + error.message);
    });
}



    function showResults(data) {
        const container = document.querySelector('.quiz-layout');
        if (!container) return;

        const percentage = (data.correctCount / data.totalQuestions) * 100;

        container.innerHTML = `
        <div class="results-container">
            <h2>Quiz Complete!</h2>
            <div class="score-section">
                <div class="score-circle" style="--score: ${data.score * 3.6}deg">
                    <div class="score-value">${data.score.toFixed(1)}%</div>
                </div>
                <div class="score-stats">
                                    <div class="stat-item">
                        <i class="fas fa-question-circle"></i>
                        <span>Total: ${data.totalQuestions}</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Correct: ${data.correctCount}</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-times-circle"></i>
                        <span>Wrong: ${data.wrongCount}</span>
                    </div>

                </div>
            </div>
            <div class="action-buttons">

                <button onclick="location.reload()" class="retry-btn">
                    <i class="fas fa-redo"></i> Try Again
                </button>
                <button onclick="window.location='/quiz '" class="home-btn">
                    <i class="fas fa-home"></i> Back to Quizzes
                </button>
            </div>
        </div>
    `;
    }


    function showAnswerReview() {
        const container = document.querySelector('.quiz-layout');
        const attemptId = container.dataset.attemptId;

        if (!attemptId) {
            console.error('No attempt ID found');
            alert('Error: No attempt ID found');
            return;
        }

        fetch(`/review/${attemptId}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Failed to load review');
                }

                let reviewHtml = `
                    <div class="review-container">
                        <h2>Answer Review</h2>
                        ${data.answers.map((item, index) => `
                            <div class="review-item ${item.is_correct ? 'correct' : 'wrong'}">
                                <div class="question-text">
                                    <strong>Question ${index + 1}:</strong> ${item.question_text}
                                </div>
                                ${item.answers.map(answer => `
        <div class="answer-option ${answer.id == item.selected_answer_id ? 
            (answer.is_correct ? 'selected-correct' : 'selected-wrong') : 
            (answer.is_correct ? 'correct-answer' : '')}">
            ${answer.answer_text}
            ${answer.id == item.selected_answer_id && !answer.is_correct ? 
                '<span class="wrong-mark">✗</span>' : ''}
            ${answer.is_correct ? '<span class="correct-mark">✓</span>' : ''}
        </div>
                                `).join('')}
                                ${item.reason ? `
                                    <button class="explanation-toggle" onclick="toggleExplanation(${index})">
                                        Show Explanation
                                    </button>
                                    <div id="explanation-${index}" class="explanation-text" style="display: none">
                                        ${item.reason}
                                    </div>
                                ` : ''}
                            </div>
                        `).join('')}
                        <div class="action-buttons">
                            <button onclick="location.reload()" class="retry-btn">
                                <i class="fas fa-redo"></i> Try Again
                            </button>
                            <button onclick="window.location='/quiz/'" class="home-btn">
                                <i class="fas fa-home"></i> Back to Quizzes
                            </button>
                        </div>
                    </div>
                `;
                container.innerHTML = reviewHtml;
            })
            .catch(error => {
                console.error('Review error:', error);
                alert('Failed to load review: ' + error.message);
            });
    }

    function toggleExplanation(index) {
        const explanation = document.getElementById(`explanation-${index}`);
        const button = explanation.previousElementSibling;
        if (explanation.style.display === 'none') {
            explanation.style.display = 'block';
            button.textContent = 'Hide Explanation';
        } else {
            explanation.style.display = 'none';
            button.textContent = 'Show Explanation';
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.option input').forEach(input => {
            input.addEventListener('change', function() {
                clearInterval(questionTimer);
                const isCorrect = this.dataset.correct === "1";
                const feedback = document.getElementById('feedbackContent');
                handleOptionSelect(this);

                if (isCorrect) {
                    feedback.innerHTML = `
                <div class="feedback-correct">Correct!</div>
                <div>Well done!</div>
            `;
                } else {
                    const correctAnswer = this.closest('.options').querySelector('input[data-correct="1"]');
                    feedback.innerHTML = `
                <div class="feedback-wrong">Incorrect!</div>
                <div>The correct answer was: ${correctAnswer.nextSibling.textContent.trim()}</div>
            `;
                }

                // Disable all options after answer
                this.closest('.question-card').querySelectorAll('input[type="radio"]').forEach(input => {
                    input.disabled = true;
                });

                // Auto advance to next question after 2 seconds
                setTimeout(() => {
                    if (currentIndex < totalQuestions - 1) {
                        nextQuestion();
                    } else {
                        submitQuiz();
                    }
                }, 2000);
            });
        });

        startQuestionTimer();
    });
</script>