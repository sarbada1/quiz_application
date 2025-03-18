<div class="quiz-container">
    <h1>Available Quizzes</h1>
    <ul class="quiz-list">
        <?php foreach($quiz as $quizdata): ?>
            <li class="quiz-item">
                <a href="<?= $url('quiz/' . $quizdata['slug']) ?>" class="quiz-name text-none">
                    <?= htmlspecialchars($quizdata['title']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include __DIR__ . '/auth/login.php'; ?>
<?php include __DIR__ . '/auth/register.php'; ?>