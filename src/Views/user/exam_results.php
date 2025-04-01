<div class="container result-container py-4">
    <div class="result-header text-center mb-4">
        <h1 class="display-4">Exam Results</h1>
        <h2><?= htmlspecialchars($exam['title']) ?></h2>
        <p class="text-muted">Completed on <?= date('F d, Y \a\t h:i A', strtotime($attempt['completed_at'])) ?></p>
    </div>
    
    <div class="row mb-5">
        <div class="col-md-8 offset-md-2">
            <div class="card result-summary">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 text-center mb-3 mb-md-0">
                            <div class="score-circle <?= $percentageScore >= 60 ? 'passing-score' : 'failing-score' ?>">
                                <div class="score-value"><?= $percentageScore ?>%</div>
                            </div>
                            <div class="mt-2 score-label">
                                <?php if ($percentageScore >= 60): ?>
                                    <span class="badge badge-success">PASSED</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">FAILED</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="result-stats-heading">Performance Summary</h5>
                            <div class="result-stats">
                                <div class="stat-item">
                                    <div class="stat-label">Total Questions</div>
                                    <div class="stat-value"><?= $totalQuestions ?></div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Attempted</div>
                                    <div class="stat-value"><?= $attemptedQuestions ?></div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Correct</div>
                                    <div class="stat-value text-success"><?= $correctAnswers ?></div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Incorrect</div>
                                    <div class="stat-value text-danger"><?= $wrongAnswers ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="result-actions mb-4 text-center">
        <a href="/student/dashboard" class="btn btn-primary mr-2">
            <i class="fas fa-home mr-1"></i> Dashboard
        </a>
        <a href="/exam/list" class="btn btn-outline-primary">
            <i class="fas fa-list mr-1"></i> All Exams
        </a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Question Review</h3>
        </div>
        <div class="card-body">
            <?php foreach ($questions as $index => $question): ?>
                <div class="question-review <?= $question['is_correct'] ? 'correct-answer' : ($question['user_answer_id'] ? 'wrong-answer' : 'not-answered') ?>">
                    <div class="question-number">Question <?= $index + 1 ?></div>
                    <div class="question-text">
                        <?= htmlspecialchars($question['question_text']) ?>
                    </div>
                    
                    <div class="options mt-3">
                        <?php foreach ($question['answers'] as $answer): ?>
                            <div class="option 
                                <?= $answer['is_correct'] ? 'option-correct' : '' ?> 
                                <?= $answer['id'] == $question['user_answer_id'] && !$answer['is_correct'] ? 'option-selected-wrong' : '' ?>
                                <?= $answer['id'] == $question['user_answer_id'] && $answer['is_correct'] ? 'option-selected-correct' : '' ?>">
                                
                                <div class="option-marker">
                                    <?php if ($answer['id'] == $question['user_answer_id'] && $answer['is_correct']): ?>
                                        <i class="fas fa-check-circle text-success"></i>
                                    <?php elseif ($answer['id'] == $question['user_answer_id'] && !$answer['is_correct']): ?>
                                        <i class="fas fa-times-circle text-danger"></i>
                                    <?php elseif ($answer['is_correct']): ?>
                                        <i class="fas fa-check text-success"></i>
                                    <?php else: ?>
                                        <i class="far fa-circle"></i>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="option-text">
                                    <?= htmlspecialchars($answer['answer']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (!$question['user_answer_id']): ?>
                        <div class="not-answered-message">
                            <i class="fas fa-exclamation-circle"></i> You did not answer this question
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($index < count($questions) - 1): ?>
                    <hr class="question-divider">
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.result-container {
    max-width: 900px;
}

.result-summary {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    border-radius: 1rem;
    overflow: hidden;
}

.score-circle {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    border: 8px solid;
}

.passing-score {
    border-color: #28a745;
    background-color: rgba(40, 167, 69, 0.1);
}

.failing-score {
    border-color: #dc3545;
    background-color: rgba(220, 53, 69, 0.1);
}

.score-value {
    font-size: 2.5rem;
    font-weight: bold;
}

.result-stats-heading {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.result-stats {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-label {
    font-weight: 500;
    color: #6c757d;
}

.stat-value {
    font-weight: bold;
    font-size: 1.1rem;
}

.question-review {
    padding: 20px;
    margin-bottom: 10px;
    border-radius: 8px;
    background-color: #f8f9fa;
}

.correct-answer {
    background-color: rgba(40, 167, 69, 0.1);
    border-left: 4px solid #28a745;
}

.wrong-answer {
    background-color: rgba(220, 53, 69, 0.1);
    border-left: 4px solid #dc3545;
}

.not-answered {
    background-color: rgba(108, 117, 125, 0.1);
    border-left: 4px solid #6c757d;
}

.question-number {
    font-weight: bold;
    color: #495057;
    margin-bottom: 5px;
}

.question-text {
    font-size: 1.1rem;
    margin-bottom: 10px;
}

.options {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.option {
    display: flex;
    padding: 10px 15px;
    border-radius: 5px;
    background-color: white;
    border: 1px solid #dee2e6;
}

.option-marker {
    margin-right: 15px;
    display: flex;
    align-items: center;
}

.option-correct {
    background-color: rgba(40, 167, 69, 0.1);
    border-color: #28a745;
}

.option-selected-wrong {
    background-color: rgba(220, 53, 69, 0.1);
    border-color: #dc3545;
}

.option-selected-correct {
    background-color: rgba(40, 167, 69, 0.1);
    border-color: #28a745;
}

.not-answered-message {
    margin-top: 15px;
    color: #6c757d;
    font-style: italic;
    display: flex;
    align-items: center;
}

.not-answered-message i {
    margin-right: 5px;
}

.question-divider {
    margin: 25px 0;
    border-top: 1px dashed #dee2e6;
}
</style>