
    

    <div class="quiz-container">
        <h1><?= htmlspecialchars($quiz['title']) ?></h1>
        
        <div class="quiz-setup active">
            <h2>Quiz Setup</h2>
            <form id="quizSetupForm">
                <label for="difficulty">Difficulty Level:</label>
                <select id="difficulty" name="difficulty" required>
                    <?php foreach ($levels as $level): ?>
                        <option value="<?= $level['id'] ?>"><?= htmlspecialchars($level['level']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="numQuestions">Number of Questions:</label>
                <input type="number" id="numQuestions" name="numQuestions" min="1" max="<?= count($questions) ?>" >
                
                <button type="submit" class="cta-button">Start Quiz</button>
            </form>
        </div>
        
        <div class="quiz-question">
            <div class="timer"></div>
            <div class="question-container">
                <h2 id="questionText"></h2>
                <div id="answerOptions"></div>
            </div>
        </div>
        
        <div class="quiz-result">
            <h2>Quiz Results</h2>
            <p>Your Score: <span id="finalScore"></span></p>
            <div id="resultDetails"></div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const quizSetup = document.querySelector('.quiz-setup');
    const quizQuestion = document.querySelector('.quiz-question');
    const quizResult = document.querySelector('.quiz-result');
    const setupForm = document.getElementById('quizSetupForm');
    const timer = document.querySelector('.timer');
    const questionText = document.getElementById('questionText');
    const answerOptions = document.getElementById('answerOptions');
    const finalScore = document.getElementById('finalScore');
    const resultDetails = document.getElementById('resultDetails');

    let questions = <?= json_encode($questions) ?>;
    
    let currentQuestionIndex = 0;
    let score = 0;
    let timeLeft = 0;
    let timerInterval;
    let userAnswers = [];

    setupForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const difficulty = document.getElementById('difficulty').value;
        const numQuestions = document.getElementById('numQuestions').value;
        
        // Filter questions based on difficulty and limit to numQuestions
        questions = questions.filter(q => q.difficulty_level == difficulty).slice(0, numQuestions);
        
        quizSetup.classList.remove('active');
        quizQuestion.classList.add('active');
        showQuestion();
    });

    function showQuestion() {
        if (currentQuestionIndex >= questions.length) {
            showResults();
            return;
        }

        const question = questions[currentQuestionIndex];
        questionText.textContent = question.question_text;
        answerOptions.innerHTML = '';

        question.answers.forEach((answer, index) => {
            const button = document.createElement('button');
            button.className = 'answer-option';
            button.textContent = answer.answer;
            button.addEventListener('click', () => selectAnswer(index));
            answerOptions.appendChild(button);
        });

        startTimer(question.time_per_question);
    }

    function selectAnswer(answerIndex) {
        clearInterval(timerInterval);
        const question = questions[currentQuestionIndex];
        const selectedAnswer = question.answers[answerIndex];
        
        userAnswers.push({
            question: question.question_text,
            userAnswer: selectedAnswer.answer,
            correct: selectedAnswer.isCorrect,
            reason: selectedAnswer.reason
        });

        if (selectedAnswer.isCorrect) {
            score++;
        }

        currentQuestionIndex++;
        showQuestion();
    }

    function startTimer(time) {
        timeLeft = time;
        updateTimerDisplay();
        timerInterval = setInterval(() => {
            timeLeft--;
            updateTimerDisplay();
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                selectAnswer(-1); // No answer selected
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        timer.textContent = `Time Left: ${timeLeft}s`;
    }

    function showResults() {
        quizQuestion.classList.remove('active');
        quizResult.classList.add('active');
        finalScore.textContent = `${score} out of ${questions.length}`;

        userAnswers.forEach((answer, index) => {
            const resultItem = document.createElement('div');
            resultItem.className = 'result-item';
            resultItem.innerHTML = `
                <p><strong>Question ${index + 1}:</strong> ${answer.question}</p>
                <p class="${answer.correct ? 'correct' : 'incorrect'}">
                    Your answer: ${answer.userAnswer}
                </p>
                <p>Correct answer: ${questions[index].answers.find(a => a.isCorrect).answer}</p>
                <p>Explanation: ${answer.reason}</p>
            `;
            resultDetails.appendChild(resultItem);
        });
    }
});
</script>