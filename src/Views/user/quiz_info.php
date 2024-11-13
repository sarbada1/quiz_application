
<div class="quiz-container">
        <h1><?= htmlspecialchars($quiz['title']) ?></h1>
        <div class="quiz-info">
            <p><strong>Category:</strong> <?= htmlspecialchars($quiz['category_name']) ?></p>
            <p><strong>Difficulty:</strong> <?= htmlspecialchars($quiz['difficulty_name']) ?></p>
            <p><strong>Number of Questions:</strong> <?= $quiz['question_count'] ?></p>
        </div>
        <div class="description">
            <h2>Description</h2>
            <p><?= nl2br(htmlspecialchars($quiz['description'])) ?></p>
        </div>
        <button class="start-quiz" onclick="startQuiz()">Start Quiz</button>
    </div>

    <div id="quizModal" class="modal">
        <div class="modal-content">
        <span class="close" data-modal="quizModal">&times;</span>
            <h2><?= htmlspecialchars($quiz['title']) ?></h2>
            <p>Please log in to start the quiz.</p>
            <button class="bg-primary text-dark text-lg" id="startQuizzing">Login</button>
        </div>
    </div>

    <?php include __DIR__ . '/auth/login.php'; ?>
    <?php include __DIR__ . '/auth/register.php'; ?>

    <script>
    function startQuiz() {
    <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
        window.location.href = '/quiz/<?= $quiz['slug'] ?>/start';
    <?php else: ?>
        document.getElementById('quizModal').style.display = 'block';
    <?php endif; ?>
}
    </script>