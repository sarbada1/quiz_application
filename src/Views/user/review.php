<div class="review-container">
    <div class="review-summary">
        <h2>Test Review</h2>
        <div class="summary-stats">
            <div class="stat">
                <span>Score</span>
                <strong><?= $attempt['obtained_marks'] ?>/<?= $attempt['total_marks'] ?></strong>
            </div>
            <div class="stat">
                <span>Percentage</span>
                <strong><?= round(($attempt['obtained_marks']/$attempt['total_marks'])*100, 2) ?>%</strong>
            </div>
        </div>
    </div>

    <?php foreach ($questions as $category => $categoryQuestions): ?>
        <div class="category-section">
            <h3><?= htmlspecialchars($category) ?></h3>
            
            <?php foreach ($categoryQuestions as $question): ?>
                <div class="question-review <?= $question['is_correct'] ? 'correct' : 'incorrect' ?>">
                    <div class="question-text">
                        <span class="question-number">Q<?= $question['question_number'] ?>.</span>
                        <?= htmlspecialchars($question['question_text']) ?>
                    </div>
                    
                    <div class="answers">
                        <?php foreach ($question['answers'] as $answer): ?>
                            <div class="answer <?= getAnswerClass($answer, $question) ?>">
                                <?= htmlspecialchars($answer['answer']) ?>
                                <?php if ($answer['id'] === $question['correct_answer_id']): ?>
                                    <span class="correct-marker">✓ Correct Answer</span>
                                <?php endif; ?>
                                <?php if ($answer['id'] === $question['user_answer_id'] && !$question['is_correct']): ?>
                                    <span class="wrong-marker">✗ Your Answer</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php
// Helper function for review page
function getAnswerClass($answer, $question) {
    if ($answer['id'] === $question['correct_answer_id']) {
        return 'correct';
    }
    if ($answer['id'] === $question['user_answer_id'] && !$question['is_correct']) {
        return 'wrong';
    }
    return '';
}?>
                    
                    <?php if (!$question['is_correct']): ?>
                        <div class="explanation">
                            <strong>Explanation:</strong> <?= htmlspecialchars($question['explanation'] ?? 'No explanation available') ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

<style>
.review-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.question-review {
    border: 1px solid #ddd;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 8px;
}

.question-review.correct {
    border-left: 4px solid #4CAF50;
}

.question-review.incorrect {
    border-left: 4px solid #f44336;
}

.answer {
    padding: 10px;
    margin: 5px 0;
    border-radius: 4px;
}

.answer.correct {
    background: #e8f5e9;
    border: 1px solid #4CAF50;
}

.answer.wrong {
    background: #ffebee;
    border: 1px solid #f44336;
}

.explanation {
    margin-top: 15px;
    padding: 15px;
    background: #fff3e0;
    border-radius: 4px;
}
</style>