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

    .option {
        display: block;
        padding: 12px;
        margin: 8px 0;
        background: #f8f9fa;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .option:hover {
        background: #e9ecef;
    }




    .score-circle {
        width: 150px;
        height: 150px;
        margin: 20px auto;
        border-radius: 50%;
        background: conic-gradient(#4CAF50 var(--score), #f0f0f0 0deg);
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
        max-width: 800px;
        margin: 100px auto;
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
        border: 1px solid #d6e9c6;
    }

    .selected-wrong {
        background-color: #ff4545;
        border: 1px solid #ebccd1;
    }

    .correct-answer {
        background-color: #7dd15a;
        border: 1px solid #d6e9c6;
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
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 4px;
    }

    .review-container {
        max-width: 800px;
        margin: 0 auto;
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



    .option {
    transition: background-color 0.3s ease;
    border-radius: 4px;
    padding: 10px;
    margin: 5px 0;
}

.option.correct {
    background-color: rgba(40, 167, 69, 0.2);
    border: 1px solid #28a745;
}

.option.incorrect {
    background-color: rgba(220, 53, 69, 0.2);
    border: 1px solid #dc3545;
}

.option.selected-correct {
    background-color: rgba(40, 167, 69, 0.4);
    border: 2px solid #28a745;
}

.option.selected-incorrect {
    background-color: rgba(220, 53, 69, 0.4);
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
        max-width: 1200px;
        margin: 140px auto;
        display: grid;
        grid-template-columns: 3fr 1fr;
        gap: 30px;
        background: #f8f9fa;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .quiz-main {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
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

    .option {
        padding: 15px 20px;
        margin: 10px 0;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }

    .option:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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
    .quiz-play-container {
    transition: opacity 0.3s ease;
}

.results-container {
    animation: slideIn 0.5s ease;
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
<div class="quiz-layout">
    <div class="quiz-main">
        <?php if (isset($attemptId)): ?>

            <form id="quizForm" data-attempt-id="<?= $attemptId ?>" data-quiz-id="<?= $quiz['id'] ?>">
                <?php foreach ($questions as $index => $question): ?>
                    <div class="question-card" id="q<?= $index ?>" style="display: <?= $index === 0 ? 'block' : 'none' ?>">
                        <h3><?= ($index + 1) . '. ' . htmlspecialchars($question['question_text']) ?></h3>
                        <div class="options">
                            <?php foreach ($question['answers'] as $answer): ?>
                                <label class="option">
                                    <input type="radio"
                                        name="q_<?= $question['id'] ?>"
                                        value="<?= $answer['id'] ?>"
                                        data-correct="<?= $answer['correct_answer'] ?>">
                                    <?= htmlspecialchars($answer['text']) ?>
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
    <div class="feedback-panel">
        <div class="time-remaining">
            <h3>Time Remaining</h3>
            <div class="question-timer">30</div>
        </div>
        <div class="answer-feedback">
            <h3>Feedback</h3>
            <div id="feedbackContent"></div>
        </div>
    </div>
</div>

<script>
    const QUESTION_TIME_LIMIT = 30; // seconds per question
    let answers = {};
    let questionTimer = null;
    let timeRemaining;

    let currentIndex = 0;
    const totalQuestions = <?= count($questions) ?>;
    let timer = null;
    let answeredQuestions = new Set();
    document.addEventListener('DOMContentLoaded', function() {
    // Initialize quiz state
    initializeQuiz();
});
function initializeQuiz() {
    // Initialize answers object
    document.querySelectorAll('.question-card').forEach((card, index) => {
        const questionId = card.dataset.questionId;
        answers[questionId] = {
            answerId: null,
            isCorrect: false,
            questionOrder: index
        };
    });

    // Initialize first question
    showQuestion(0);
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
    function startTimer() {
        const startTime = Date.now();
        timer = setInterval(() => {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;

            document.getElementById('timer').textContent =
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
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

function submitQuiz() {
    const form = document.getElementById('quizForm');
    if (!form) {
        console.error('Quiz form not found');
        return;
    }

    const attemptId = form.dataset.attemptId;
    
    // Count correct/wrong answers
    let correctCount = 0;
    let wrongCount = 0;
    Object.values(answers).forEach(answer => {
        if (answer.isCorrect) correctCount++;
        else if (answer.answerId !== null) wrongCount++;
    });

    const totalQuestions = document.querySelectorAll('.question-card').length;

    fetch('/api/quiz/submit', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify({
            attemptId: attemptId,
            answers: answers,
            correctCount: correctCount,
            wrongCount: wrongCount,
            totalQuestions: totalQuestions,
            score: (correctCount / totalQuestions) * 100
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showResults(data);
        } else {
            throw new Error(data.error || 'Failed to submit quiz');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to submit quiz. Please try again.');
    });
}

    function showResults(data) {
        if (!data) {
        console.error('No data provided to showResults');
        return;
    }
    clearInterval(timer); // Clear any remaining timers
    clearInterval(questionTimer);
    
    const container = document.querySelector('.quiz-play-container');
    if (!container) {
        console.error('Quiz container not found');
        return;
    }

    container.dataset.attemptId = data.attemptId;
    container.style.opacity = '0';
    
    setTimeout(() => {
        container.innerHTML = `
        <div class="results-container animate__animated animate__fadeIn">
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
                <button onclick="showAnswerReview()" class="review-btn">
                    <i class="fas fa-book-open"></i> Review Answers
                </button>
                <button onclick="location.reload()" class="retry-btn">
                    <i class="fas fa-redo"></i> Try Again
                </button>
                <button onclick="window.location='/quiz-play/quiz/'" class="home-btn">
                    <i class="fas fa-home"></i> Back to Quizzes
                </button>
            </div>
        </div>`;
        
        // Fade in new content
        container.style.opacity = '1';
        const reviewBtn = container.querySelector('.review-btn');
        if (reviewBtn) {
            reviewBtn.addEventListener('click', showAnswerReview);
        }
        // Scroll to results
        container.scrollIntoView({ behavior: 'smooth' });
    }, 300);
}


    function showAnswerReview() {
        const container = document.querySelector('.quiz-play-container');
        const attemptId = container.dataset.attemptId;

        if (!attemptId) {
            console.error('No attempt ID found');
            alert('Error: No attempt ID found');
            return;
        }

        fetch(`/quiz-play/review/${attemptId}`)
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
                            <button onclick="window.location='/quiz-play/quiz/'" class="home-btn">
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