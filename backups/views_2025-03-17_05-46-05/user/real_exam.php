<div id="examContainer" data-quiz-id="<?= $quiz['id'] ?>">
    <div class="header">
        <h2><?= htmlspecialchars($quiz['title']) ?></h2>
        <div id="timer">--:--:--</div>
    </div>
    <div class="questions">
        <?php foreach ($questions as $index => $question): ?>
            <div class="question" data-question-id="<?= $question['id'] ?>">
                <p><?= htmlspecialchars($question['question_text']) ?></p>
                <?php foreach ($question['answers'] as $answer): ?>
                    <label>
                        <input type="radio" name="question_<?= $question['id'] ?>" value="<?= $answer['id'] ?>">
                        <?= htmlspecialchars($answer['answer']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <button onclick="submitExam()">Submit</button>
</div>

<script>
    const socket = new WebSocket('ws://localhost:8080');
    socket.onmessage = function(event) {
        const data = JSON.parse(event.data);
        if (data.type === 'timer') {
            document.getElementById('timer').textContent = data.time;
        }
    };

    function submitExam() {
        const quizId = document.getElementById('examContainer').dataset.quizId;
        const answers = {};
        document.querySelectorAll('input[type="radio"]:checked').forEach(input => {
            answers[input.name] = input.value;
        });
        fetch('/ajax/submit-exam', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ quizId, answers })
        }).then(response => response.json()).then(data => {
            if (data.success) {
                alert('Exam submitted successfully!');
            } else {
                alert('Failed to submit exam.');
            }
        });
    }
</script>