<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Student Exam Details</h1>
        <div>
            <a href="<?= $url('admin/exam/results/' . $exam['id']) ?>" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-arrow-left mr-1"></i> Back to Results
            </a>
            <?php if (!$attempt['is_published']): ?>
                <button id="publishResult" class="btn btn-sm btn-success ml-2" data-attempt-id="<?= $attempt['id'] ?>">
                    <i class="fas fa-check-circle mr-1"></i> Publish Result
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Student Info Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Student
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= htmlspecialchars($student['name'] ?? 'Unknown') ?>
                            </div>
                            <div class="text-sm text-muted">
                                <?= htmlspecialchars($student['email'] ?? '') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Info Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Exam
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= htmlspecialchars($exam['title']) ?>
                            </div>
                            <div class="text-sm text-muted">
                                <?= date('F d, Y', strtotime($attempt['completed_at'])) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Score Info Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card <?= $percentageScore >= 60 ? 'border-left-success' : 'border-left-danger' ?> shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold <?= $percentageScore >= 60 ? 'text-success' : 'text-danger' ?> text-uppercase mb-1">
                                Score
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $percentageScore ?>%
                            </div>
                            <div class="text-sm text-muted">
                                <?= $correctAnswers ?> correct / <?= $wrongAnswers ?> incorrect
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas <?= $percentageScore >= 60 ? 'fa-check-circle' : 'fa-times-circle' ?> fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Publication Status -->
    <div class="card mb-4 py-3 <?= $attempt['is_published'] ? 'border-left-success' : 'border-left-warning' ?>">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <i class="fas <?= $attempt['is_published'] ? 'fa-check-circle text-success' : 'fa-clock text-warning' ?> fa-2x mr-3"></i>
                </div>
                <div class="col">
                    <h6 class="font-weight-bold mb-1">
                        <?= $attempt['is_published'] ? 'Results Published' : 'Results Pending Publication' ?>
                    </h6>
                    <p class="mb-0">
                        <?php if ($attempt['is_published']): ?>
                            The student can view their results.
                        <?php else: ?>
                            The student cannot view their results until you publish them.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Question Review -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Question Review</h6>
        </div>
        <div class="card-body">
            <?php foreach ($questions as $index => $question): ?>
                <div class="question-review mb-4 <?= $question['is_correct'] ? 'correct-answer' : ($question['user_answer_id'] ? 'wrong-answer' : 'not-answered') ?>">
                    <div class="question-header d-flex justify-content-between">
                        <div class="question-number">
                            <strong>Question <?= $index + 1 ?></strong>
                        </div>
                        <div class="question-status">
                            <?php if (!$question['user_answer_id']): ?>
                                <span class="badge badge-secondary">Not Answered</span>
                            <?php elseif ($question['is_correct']): ?>
                                <span class="badge badge-success">Correct</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Incorrect</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="question-text mt-2">
                        <?= htmlspecialchars($question['question_text']) ?>
                    </div>
                    
                    <div class="options mt-3">
                        <?php foreach ($question['answers'] as $answer): ?>
                            <div class="option d-flex align-items-center p-2 mb-2 
                                <?= $answer['is_correct'] ? 'option-correct' : '' ?> 
                                <?= $answer['id'] == $question['user_answer_id'] && !$answer['is_correct'] ? 'option-selected-wrong' : '' ?>
                                <?= $answer['id'] == $question['user_answer_id'] && $answer['is_correct'] ? 'option-selected-correct' : '' ?>">
                                
                                <div class="option-marker mr-3">
                                    <?php if ($answer['id'] == $question['user_answer_id'] && $answer['is_correct']): ?>
                                        <i class="fas fa-check-circle text-success"></i>
                                    <?php elseif ($answer['id'] == $question['user_answer_id'] && !$answer['is_correct']): ?>
                                        <i class="fas fa-times-circle text-danger"></i>
                                    <?php elseif ($answer['is_correct']): ?>
                                        <i class="fas fa-check text-success"></i>
                                    <?php else: ?>
                                        <i class="far fa-circle text-muted"></i>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="option-text flex-grow-1">
                                    <?= htmlspecialchars($answer['answer']) ?>
                                </div>
                                
                                <?php if ($answer['id'] == $question['user_answer_id']): ?>
                                    <div class="option-badge">
                                        <span class="badge badge-primary">Student Answer</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if ($index < count($questions) - 1): ?>
                    <hr>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.question-review {
    padding: 15px;
    border-radius: 5px;
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

.option {
    border-radius: 5px;
    background-color: white;
    border: 1px solid #dee2e6;
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Publish result functionality
    const publishButton = document.getElementById('publishResult');
    if (publishButton) {
        publishButton.addEventListener('click', function() {
            const attemptId = this.dataset.attemptId;
            
            if (confirm('Are you sure you want to publish this result? The student will be able to see it.')) {
                fetch('/api/admin/exam/publish-results', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        attempt_ids: [attemptId]
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Result published successfully!');
                        location.reload();
                    } else {
                        alert('Failed to publish result. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        });
    }
});
</script>