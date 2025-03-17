<div class="container">
    <h2>Previous Year Quizzes</h2>
    <div class="quiz-list">
        <?php if (!empty($quizzes)): ?>
            <?php foreach ($quizzes as $quiz): ?>
                <div class="quiz-card">
                    <h3><?= htmlspecialchars($quiz['title']) ?></h3>
                    <p><?= htmlspecialchars($quiz['description']) ?></p>
                    <a href="<?= $url('previous-year-quiz/<?= $quiz[') ?>"id'] ?>" class="btn btn-primary">Start Quiz</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No previous year quizzes available.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.container {
    max-width: 800px;
    margin: 150px auto;
    padding: 20px;
}

.quiz-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin: 90px auto;

}

.quiz-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.quiz-card h3 {
    margin-bottom: 10px;
}

.quiz-card p {
    margin-bottom: 20px;
}

.btn {
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    color: white;
}

.btn-primary {
    background-color: #007bff;
}
</style>