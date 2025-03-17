<div class="quiz-play-container">
    <div class="quiz-header">
        <h1>Custom Quiz</h1>
        <div class="quiz-info">
            <div class="timer" id="timer">00:00</div>
        </div>
    </div>

    <?php if (isset($attemptId)): ?>
        <form id="quizForm" data-attempt-id="<?= $attemptId ?>">
            <?php foreach ($questions as $index => $question): ?>
                <div class="question-card" id="q<?= $index ?>" style="display: <?= $index === 0 ? 'block' : 'none' ?>">
                    <h3><?= ($index + 1) . '. ' . htmlspecialchars($question['question_text']) ?></h3>
                    <div class="options">
                        <?php foreach ($question['answers'] as $answer): ?>
                            <label class="option">
                                <input type="radio" 
                                       name="q_<?= $question['id'] ?>" 
                                       value="<?= $answer['id'] ?>"
                                       data-correct="<?= $answer['is_correct'] ?>">
                                <?= htmlspecialchars($answer['text']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="quiz-controls">
                <button type="button" id="prevBtn" onclick="prevQuestion()">Previous</button>
                <button type="button" id="nextBtn" onclick="nextQuestion()">Next</button>
                <button type="button" id="submitBtn" onclick="submitQuiz()" style="display:none">Submit Quiz</button>
            </div>
        </form>
    <?php else: ?>
        <div class="error">Error: No attempt ID found. Please try again.</div>
    <?php endif; ?>
</div>

<script>
    let currentIndex = 0;
    const totalQuestions = <?= count($questions) ?>;
    let timer;
    let answeredQuestions = new Set();

    document.addEventListener('DOMContentLoaded', function() {
        startTimer();
        showQuestion(currentIndex);
        updateNavButtons();
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

    function showQuestion(index) {
        document.querySelectorAll('.question-card').forEach(q => q.style.display = 'none');
        document.getElementById(`q${index}`).style.display = 'block';
        updateNavButtons();
    }

    function prevQuestion() {
        if (currentIndex > 0) {
            currentIndex--;
            showQuestion(currentIndex);
        }
    }

    function nextQuestion() {
        if (currentIndex < totalQuestions - 1) {
            currentIndex++;
            showQuestion(currentIndex);
        }
    }

    function updateNavButtons() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        prevBtn.style.display = currentIndex === 0 ? 'none' : 'block';
        
        if (currentIndex >= totalQuestions - 1) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'block';
        } else {
            nextBtn.style.display = 'block';
            submitBtn.style.display = 'none';
        }
    }

    function submitQuiz() {
        if (!confirm('Are you sure you want to submit?')) return;

        clearInterval(timer);
        const form = document.getElementById('quizForm');
        const attemptId = form.dataset.attemptId;

        const answers = {};
        let correctCount = 0;
        let wrongCount = 0;

        document.querySelectorAll('.question-card').forEach((card, index) => {
            const selectedInput = card.querySelector('input[type="radio"]:checked');
            const questionId = card.querySelector('input[type="radio"]').name.split('_')[1];

            if (selectedInput) {
                const isCorrect = selectedInput.dataset.correct === "1";
                answers[questionId] = {
                    answerId: selectedInput.value,
                    isCorrect: isCorrect,
                    questionOrder: index
                };

                if (isCorrect) correctCount++;
                else wrongCount++;
            }
        });

        fetch('<?= $url('quiz/submit') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                attemptId: attemptId,
                answers: answers,
                correctCount: correctCount,
                wrongCount: wrongCount,
                totalQuestions: totalQuestions
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = `/review/${attemptId}`;
            } else {
                alert('Failed to submit quiz: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Submission error:', error);
            alert('Failed to submit quiz');
        });
    }
</script>

<style>
    .quiz-play-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
    }

    .quiz-header {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .question-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .options {
        display: grid;
        gap: 10px;
        margin-top: 15px;
    }

    .option {
        display: block;
        padding: 12px 15px;
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
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

    #prevBtn, #nextBtn {
        background: #3498db;
        color: white;
    }

    #submitBtn {
        background: #e74c3c;
        color: white;
    }
</style>