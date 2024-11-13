<?php
// user/question.php
?>

<div class="quiz-container">
    <div id="quiz-progress" class="mb-4">
        <div class="progress-bar" style="width: 0%"></div>
    </div>
    
    <?php foreach ($questions as $index => $question): ?>
        <div class="question-card" id="question-<?= $index ?>" style="display: <?= $index === 0 ? 'block' : 'none' ?>;">
            <h2 class="question-text mb-4"><?= htmlspecialchars($question['question_text']) ?></h2>
            <p class="question-type"><?= htmlspecialchars($question['type']) ?></p>
            <p class="time-limit">Time limit: <?= $question['time_per_question'] ?> seconds</p>
            
            <div class="answers-container">
                <?php foreach ($question['answers'] as $answer): ?>
                    <div class="answer-option mb-3">
                        <input type="radio" id="answer-<?= $answer['id'] ?>" name="question-<?= $question['id'] ?>" value="<?= $answer['id'] ?>">
                        <label for="answer-<?= $answer['id'] ?>"><?= htmlspecialchars($answer['answer']) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="explanation" style="display: none;"></div>
            
            <div class="mt-4">
                <button class="btn btn-primary next-question" <?= $index === count($questions) - 1 ? 'style="display:none;"' : '' ?>>Next Question</button>
                <button class="btn btn-success submit-quiz" <?= $index !== count($questions) - 1 ? 'style="display:none;"' : '' ?>>Submit Quiz</button>
            </div>
        </div>
    <?php endforeach; ?>
    
    <div id="quiz-results" style="display: none;">
        <h2>Quiz Results</h2>
        <p>Your score: <span id="score"></span></p>
        <button id="review-answers" class="btn btn-primary">Review Answers</button>
        <button id="play-again" class="btn btn-success">Play Again</button>
    </div>
</div>

<script>
let currentQuestion = 0;
const totalQuestions = <?= count($questions) ?>;
let timer;
let userAnswers = [];
let quizSubmitted = false;

function startTimer(seconds) {
    let timeLeft = seconds;
    timer = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(timer);
            moveToNextQuestion();
        } else {
            document.querySelector('.time-limit').textContent = `Time left: ${timeLeft} seconds`;
            timeLeft--;
        }
    }, 1000);
}

function moveToNextQuestion() {
    clearInterval(timer);
    if (currentQuestion < totalQuestions - 1) {
        saveAnswer();
        document.getElementById(`question-${currentQuestion}`).style.display = 'none';
        currentQuestion++;
        document.getElementById(`question-${currentQuestion}`).style.display = 'block';
        updateProgress();
        startTimer(<?= $questions[0]['time_per_question'] ?>);
    } else {
        submitQuiz();
    }
}

function saveAnswer() {
    const selectedAnswer = document.querySelector(`input[name="question-${<?= $questions[0]['id'] ?>}"]:checked`);
    userAnswers[currentQuestion] = selectedAnswer ? selectedAnswer.value : null;
}

document.querySelectorAll('.next-question').forEach(button => {
    button.addEventListener('click', moveToNextQuestion);
});

document.querySelector('.submit-quiz').addEventListener('click', submitQuiz);

function submitQuiz() {
    clearInterval(timer);
    saveAnswer();
    quizSubmitted = true;
    // Here you would typically submit the quiz answers to the server
    displayResults();
}

function displayResults() {
    const quizContainer = document.querySelector('.quiz-container');
    const resultsDiv = document.getElementById('quiz-results');
    quizContainer.style.display = 'none';
    resultsDiv.style.display = 'block';
    
    // Calculate score (this is a placeholder, replace with actual scoring logic)
    const score = userAnswers.filter(answer => answer !== null).length;
    document.getElementById('score').textContent = `${score}/${totalQuestions}`;
}

function reviewAnswers() {
    const resultsDiv = document.getElementById('quiz-results');
    resultsDiv.style.display = 'none';
    document.querySelector('.quiz-container').style.display = 'block';
    showQuestion(0);
}

function showQuestion(index) {
    document.querySelectorAll('.question-card').forEach(card => card.style.display = 'none');
    const questionCard = document.getElementById(`question-${index}`);
    questionCard.style.display = 'block';
    
    if (quizSubmitted) {
        const correctAnswerId = <?= json_encode(array_column($questions, 'correct_answer_id')) ?>[index];
        const explanation = <?= json_encode(array_column($questions, 'explanation')) ?>[index];
        
        questionCard.querySelectorAll('.answer-option').forEach(option => {
            const input = option.querySelector('input');
            const label = option.querySelector('label');
            
            if (input.value == correctAnswerId) {
                label.style.color = 'green';
            } else if (input.value == userAnswers[index]) {
                label.style.color = 'red';
            }
            
            input.disabled = true;
        });
        
        const explanationDiv = questionCard.querySelector('.explanation');
        explanationDiv.textContent = explanation;
        explanationDiv.style.display = 'block';
    }
    
    updateNavButtons(index);
}

function updateNavButtons(index) {
    const prevButton = document.createElement('button');
    prevButton.textContent = 'Previous';
    prevButton.className = 'btn btn-secondary mr-2';
    prevButton.onclick = () => showQuestion(index - 1);
    prevButton.style.display = index === 0 ? 'none' : 'inline-block';
    
    const nextButton = document.createElement('button');
    nextButton.textContent = index === totalQuestions - 1 ? 'Finish Review' : 'Next';
    nextButton.className = 'btn btn-primary';
    nextButton.onclick = index === totalQuestions - 1 ? displayResults : () => showQuestion(index + 1);
    
    const navDiv = questionCard.querySelector('.mt-4');
    navDiv.innerHTML = '';
    navDiv.appendChild(prevButton);
    navDiv.appendChild(nextButton);
}

document.getElementById('review-answers').addEventListener('click', reviewAnswers);
document.getElementById('play-again').addEventListener('click', () => {
    location.reload(); // This will restart the quiz by reloading the page
});

function updateProgress() {
    const progress = ((currentQuestion + 1) / totalQuestions) * 100;
    document.querySelector('.progress-bar').style.width = `${progress}%`;
}

updateProgress();
startTimer(<?= $questions[0]['time_per_question'] ?>);
</script>