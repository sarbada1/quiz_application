<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Exam Results: <?= htmlspecialchars($exam['title']) ?></h1>
        <div>
            <a href="<?= $url('admin/quiz/list') ?>" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-arrow-left mr-1"></i> Back to Exams
            </a>
            <button id="publishSelected" class="btn btn-sm btn-success ml-2" disabled>
                <i class="fas fa-check-circle mr-1"></i> Publish Selected
            </button>
        </div>
    </div>

    <?php if (empty($attempts)): ?>
        <div class="card shadow mb-4">
            <div class="card-body text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" alt="No attempts" style="width: 100px; opacity: 0.5; margin-bottom: 20px;">
                <h4 class="text-muted">No Attempts Yet</h4>
                <p>There are no student attempts for this exam yet.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Student Submissions</h6>
                <div class="form-inline">
                    <div class="form-check mr-3">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label" for="selectAll">Select All</label>
                    </div>
                    <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search students...">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="resultsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="50px">Select</th>
                                <th>Student</th>
                                <th>Score</th>
                                <th>Correct</th>
                                <th>Incorrect</th>
                                <th>Completion Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attempts as $attempt): ?>
                                <tr data-attempt-id="<?= $attempt['id'] ?>">
                                    <td class="text-center">
                                        <input type="checkbox" class="attempt-checkbox" 
                                            data-attempt-id="<?= $attempt['id'] ?>" 
                                            <?= $attempt['is_published'] ? 'disabled checked' : '' ?>>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                         
                                            <div>
                                                <div><?= htmlspecialchars($attempt['student_name'] ?? 'Unknown') ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($attempt['student_email'] ?? '') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                        $totalQuestions = ($attempt['correct_answers'] ?? 0) + ($attempt['wrong_answers'] ?? 0);
                                        $percentage = $totalQuestions > 0 ? round(($attempt['score'] / $totalQuestions) * 100, 2) : 0;
                                        
                                        $scoreBadgeClass = 'badge-';
                                        if ($percentage >= 80) $scoreBadgeClass .= 'success';
                                        else if ($percentage >= 60) $scoreBadgeClass .= 'primary';
                                        else if ($percentage >= 40) $scoreBadgeClass .= 'warning';
                                        else $scoreBadgeClass .= 'danger';
                                        ?>
                                        <span class="badge <?= $scoreBadgeClass ?>"><?= $percentage ?>%</span>
                                        <div class="small mt-1"><?= $attempt['score'] ?>/<?= $totalQuestions ?> points</div>
                                    </td>
                                    <td>
                                        <span class="text-success"><?= $attempt['correct_answers'] ?? 0 ?></span>
                                    </td>
                                    <td>
                                        <span class="text-danger"><?= $attempt['wrong_answers'] ?? 0 ?></span>
                                    </td>
                                    <td>
                                        <?= date('M d, Y h:i A', strtotime($attempt['completed_at'])) ?>
                                    </td>
                                    <td>
                                        <?php if ($attempt['is_published']): ?>
                                            <span class="badge badge-success">Published</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= $url('admin/exam/student-result/' . $attempt['id']) ?>" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                        <?php if (!$attempt['is_published']): ?>
                                            <button class=" success btn btn-sm success publish-btn" data-attempt-id="<?= $attempt['id'] ?>">
                                                <i class="fas fa-check mr-1"></i> Publish
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#resultsTable tbody tr');
            
            tableRows.forEach(row => {
                const studentName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                if (studentName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.attempt-checkbox:not([disabled])');
    const publishSelectedBtn = document.getElementById('publishSelected');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updatePublishButton();
        });
    }
    
    // Individual checkbox change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updatePublishButton);
    });
    
    // Update publish button state
    function updatePublishButton() {
        const checkedBoxes = document.querySelectorAll('.attempt-checkbox:checked:not([disabled])');
        if (publishSelectedBtn) {
            publishSelectedBtn.disabled = checkedBoxes.length === 0;
        }
    }
    
    // Publish selected attempts
    if (publishSelectedBtn) {
        publishSelectedBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.attempt-checkbox:checked:not([disabled])');
            const attemptIds = Array.from(checkedBoxes).map(box => box.dataset.attemptId);
            
            if (attemptIds.length === 0) return;
            
            // Confirm before publishing
            if (confirm(`Are you sure you want to publish ${attemptIds.length} results?`)) {
                publishResults(attemptIds);
            }
        });
    }
    
    // Individual publish buttons
    const publishButtons = document.querySelectorAll('.publish-btn');
    publishButtons.forEach(button => {
        button.addEventListener('click', function() {
            const attemptId = this.dataset.attemptId;
            if (confirm('Are you sure you want to publish this result?')) {
                publishResults([attemptId]);
            }
        });
    });
    
    // Function to publish results
    function publishResults(attemptIds) {
        fetch('/api/admin/exam/publish-results', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                attempt_ids: attemptIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Results published successfully!');
                // Refresh the page to show updated status
                location.reload();
            } else {
                alert('Failed to publish results. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
});
</script>