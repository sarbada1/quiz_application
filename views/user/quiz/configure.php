
<div class="container my-5">
    <h2 class="text-center mb-4">Customize Your Quiz</h2>
    <form action="/quiz/custom" method="POST" class="quiz-config">
        <div class="row">
            <!-- Category Selection -->
            <div class="col-md-4">
                <div class="config-box">
                    <h3>Select Category</h3>
                    <div class="category-options">
                        <?php foreach ($categories as $category): ?>
                        <div class="option-card">
                            <input type="radio" name="category_id" id="cat_<?= $category['id'] ?>" value="<?= $category['id'] ?>">
                            <label for="cat_<?= $category['id'] ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Difficulty Level Selection -->
            <div class="col-md-4">
                <div class="config-box">
                    <h3>Select Difficulty</h3>
                    <div class="level-options">
                        <?php foreach ($levels as $level): ?>
                        <div class="option-card">
                            <input type="radio" name="level_id" id="level_<?= $level['id'] ?>" value="<?= $level['id'] ?>">
                            <label for="level_<?= $level['id'] ?>">
                                <?= htmlspecialchars($level['name']) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Question Count Selection -->
            <div class="col-md-4">
                <div class="config-box">
                    <h3>Number of Questions</h3>
                    <div class="question-count-options">
                        <?php foreach ([5, 10, 15, 20, 25] as $count): ?>
                        <div class="option-card">
                            <input type="radio" name="question_count" id="count_<?= $count ?>" value="<?= $count ?>">
                            <label for="count_<?= $count ?>"><?= $count ?> Questions</label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary btn-lg">Start Quiz</button>
        </div>
    </form>
</div>

<style>
.quiz-config .config-box {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    height: 100%;
}

.quiz-config h3 {
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}

.option-card {
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.option-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.option-card input[type="radio"] {
    display: none;
}

.option-card label {
    display: block;
    padding: 15px;
    cursor: pointer;
    text-align: center;
    width: 100%;
    margin: 0;
}

.option-card input[type="radio"]:checked + label {
    background: #007bff;
    color: white;
    border-radius: 6px;
}

.btn-primary {
    padding: 12px 40px;
    font-size: 1.1em;
}
</style>