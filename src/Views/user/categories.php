<div class="category-header">
    <h1><?= htmlspecialchars($category['name']) ?></h1>
</div>
<div class="breadcrumb">
<a href="/"><i class="fas fa-home"></i></a>
<span class="separator">/</span>
    <a href="#" style="cursor:default"><?=$category['name']?></a>
</div>
<?php if (empty($quizzes)): ?>
    <p class="no-quizzes">No quizzes available in this category yet.</p>
<?php else: ?>
    <div class="quiz-grid">
        <?php foreach ($quizzes as $quiz): ?>
            <div class="quiz-card">
                <div class="quiz-card-content">
                    <h2><?= htmlspecialchars($quiz['title']) ?></h2>
                    <p><?= htmlspecialchars($quiz['description']) ?></p>
                    <button class="primary"> <a href="/quiz/<?= htmlspecialchars($quiz['slug']) ?>">Start Quiz</a></button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>