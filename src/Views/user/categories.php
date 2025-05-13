<div class="category-container">
    <?php if (!empty($c_quizzes)): ?>
        <div class="quiz-grid">
            <?php foreach ($c_quizzes as $quiz): ?>
                <div class="quiz-card">
                    <div class="quiz-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="quiz-card-content">
                        <h2><?= htmlspecialchars($quiz['title']) ?></h2>
                        <p><?= htmlspecialchars($quiz['description']) ?></p>
                        <div class="quiz-meta">
                            <span class="questions-count">
                                <i class="fas fa-question-circle"></i>
                                <?= $quiz['question_count'] ?? 0 ?> Questions
                            </span>
                         
                        </div>
                        <?php
                        if($quiz['question_count']!=0){
                            ?>
           <button class="quiz-btn">
    <a href="<?= $url('quiz/' . htmlspecialchars($quiz['slug'])) ?>">
        Start Quiz <i class="fas fa-arrow-right"></i>
    </a>
</button>
                        <?php
                    }?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-quizzes">
            <i class="fas fa-info-circle"></i>
            <p>No quizzes available for this category.</p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/auth/login.php'; ?>
<?php include __DIR__ . '/auth/register.php'; ?>
<style>
.category-container {
    max-width: 1200px;
    margin: 230px auto;
    padding: 0 1rem;
}

.quiz-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.quiz-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    position: relative;
}

.quiz-card:hover {
    transform: translateY(-5px);
}

.quiz-icon {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    padding: 2rem;
    text-align: center;
}

.quiz-icon i {
    font-size: 2.5rem;
}

.quiz-card-content {
    padding: 1.5rem;
}

.quiz-card h2 {
    color: #1f2937;
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
}

.quiz-card p {
    color: #6b7280;
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.quiz-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.quiz-meta span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quiz-btn {
    width: 100%;
    background: #6366f1;
    border: none;
    border-radius: 6px;
    transition: background-color 0.3s ease;
}

.quiz-btn:hover {
    background: #4f46e5;
}

.quiz-btn a {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    color: white;
    text-decoration: none;
    font-weight: 500;
}

.no-quizzes {
    text-align: center;
    padding: 3rem;
    color: #6b7280;
}

.no-quizzes i {
    font-size: 3rem;
    margin-bottom: 1rem;
}
</style>