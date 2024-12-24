<!-- quiz_configure.php -->
<div class="quiz-config-container">
    <h2 class="config-title">Customize Your Quiz</h2>
    
    <form action="/quiz/custom" method="POST" class="quiz-config-form">
        <div class="config-grid">
            <!-- Category Selection -->
            <div class="config-section">
                <h3>Select Category</h3>
                <div class="option-grid">
                    <?php foreach ($categories as $category): ?>
                    <div class="option-card">
                        <input type="radio" name="category_id" id="cat_<?= $category['id'] ?>" value="<?= $category['id'] ?>" required>
                        <label for="cat_<?= $category['id'] ?>">
                            <div class="card-content">
                                <i class="fas fa-folder category-icon"></i>
                                <span class="option-title"><?= htmlspecialchars($category['name']) ?></span>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Difficulty Selection -->
            <div class="config-section">
                <h3>Select Difficulty</h3>
                <div class="option-grid">
                    <?php foreach ($levels as $level): ?>
                    <div class="option-card">
                        <input type="radio" name="level_id" id="level_<?= $level['id'] ?>" value="<?= $level['id'] ?>" required>
                        <label for="level_<?= $level['id'] ?>">
                            <div class="card-content">
                                <i class="fas fa-signal difficulty-icon"></i>
                                <span class="option-title"><?= htmlspecialchars($level['level']) ?></span>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Question Count Selection -->
            <div class="config-section">
                <h3>Number of Questions</h3>
                <div class="option-grid questions-grid">
                    <?php foreach ([5, 10, 15, 20, 25] as $count): ?>
                    <div class="option-card">
                        <input type="radio" name="question_count" id="count_<?= $count ?>" value="<?= $count ?>" required>
                        <label for="count_<?= $count ?>">
                            <div class="card-content">
                                <i class="fas fa-question-circle question-icon"></i>
                                <span class="option-title"><?= $count ?></span>
                                <span class="option-subtitle">Questions</span>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <button type="submit" class="start-quiz-btn">
            <i class="fas fa-play"></i> Start Custom Quiz
        </button>
    </form>
</div>

<style>
.quiz-config-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

.config-title {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 40px;
    font-size: 2.5em;
    font-weight: 600;
}

.config-grid {
    display: grid;
    gap: 30px;
    margin-bottom: 40px;
}

.config-section {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.config-section h3 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 1.5em;
    font-weight: 500;
}

.option-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 15px;
}

.questions-grid {
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
}

.option-card {
    position: relative;
}

.option-card input[type="radio"] {
    display: none;
}

.option-card label {
    display: block;
    padding: 20px;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.option-card:hover label {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.option-card input[type="radio"]:checked + label {
    background: #4CAF50;
    border-color: #4CAF50;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.card-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 10px;
}

.card-content i {
    font-size: 24px;
    margin-bottom: 5px;
}

.option-title {
    font-size: 16px;
    font-weight: 500;
}

.option-subtitle {
    font-size: 14px;
    opacity: 0.8;
}

.start-quiz-btn {
    display: block;
    width: 100%;
    max-width: 300px;
    margin: 0 auto;
    padding: 15px 30px;
    font-size: 1.2em;
    color: white;
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    border: none;
    border-radius: 30px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

.start-quiz-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
}

.start-quiz-btn i {
    margin-right: 10px;
}

@media (max-width: 768px) {
    .config-grid {
        grid-template-columns: 1fr;
    }
    
    .option-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    }
}
</style>