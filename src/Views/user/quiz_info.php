<!-- quiz_info.php -->
<div class="quiz-info-container mt-10">
    <div class="quiz-header">
        <h1><?= htmlspecialchars($quiz['title']) ?></h1>
        <div class="quiz-meta">
            <span class="category"><i class="fas fa-folder"></i> <?= htmlspecialchars($quiz['category_name']) ?></span>
            <span class="difficulty"><i class="fas fa-signal"></i> <?= htmlspecialchars($quiz['difficulty_name']) ?></span>
            <span class="questions"><i class="fas fa-question-circle"></i> <?= $quiz['question_count'] ?> Questions Available</span>
        </div>
    </div>

    <div class="quiz-description">
        <h2>About This Quiz</h2>
        <p><?= nl2br(htmlspecialchars($quiz['description'])) ?></p>
    </div>

    <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
        <div class="quiz-preferences">
            <h2>Customize Your Quiz</h2>
            <form id="quizPreferenceForm">
                <div class="form-group">
                    <label for="questionCount">Number of Questions:</label>
                    <select id="questionCount" name="count" required>
                        <?php foreach ([5, 10, 15, 20, 25, 30] as $value): ?>
                            <option value="<?= $value ?>"><?= $value ?> Questions</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="button" onclick="startQuiz()" class="start-quiz-btn">Start Quiz</button>
            </form>
        </div>
    <?php else: ?>
        <div id="quizModal" class="modal">
            <div class="modal-content">
                <span class="close" data-modal="quizModal">&times;</span>
                <h2><?= htmlspecialchars($quiz['title']) ?></h2>
                <p>Please log in to start the quiz.</p>
                <button class="login-btn" id="startQuizzing">Login</button>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/auth/login.php'; ?>
<?php include __DIR__ . '/auth/register.php'; ?>

<style>
    .quiz-info-container {
        max-width: 800px;
        margin: 7rem auto 0 auto;
        padding: 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .quiz-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .quiz-meta {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-top: 1rem;
        color: #666;
    }

    .quiz-meta span {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .quiz-description {
        margin: 2rem 0;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .quiz-preferences {
        margin-top: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    #questionCount {
        width: 200px;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .category-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 0.5rem;
    }

    .category-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 4px;
        cursor: pointer;
    }

    .category-option:hover {
        background: #e9ecef;
    }

    .form-actions {
        text-align: center;
        margin-top: 2rem;
    }

    .start-quiz-btn {
        padding: 12px 24px;
        font-size: 1.1rem;
        background: linear-gradient(to right, #4CAF50, #45a049);
        color: white;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .start-quiz-btn:hover {
        transform: translateY(-2px);
    }

    .login-btn {
        padding: 10px 20px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .login-btn:hover {
        background: #0056b3;
    }
</style>

<script>
    function startQuiz() {
        const count = document.getElementById('questionCount').value;
        window.location.href="<?= $url('quiz/<?= $quiz[') ?>"slug'] ?>/start/' + count;
    }
</script>