<?php if (isset($_SESSION['message'])): ?>
    <div id="alert" class="alert alert-<?= $_SESSION['status'] ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= $_SESSION['message'] ?>
    </div>
<?php
    unset($_SESSION['message']);
    unset($_SESSION['status']);
endif;
?>
<div class="mock-config-container">
    <h2>Configure Mock Test: <?= htmlspecialchars($quiz['title']) ?></h2>

    <div class="configuration-info">
        <div class="info-box">
            <h3>Total Marks: <?= $quiz['total_marks'] ?></h3>
            <p>Remaining: <span id="remaining-marks"><?= $quiz['total_marks'] ?></span></p>
        </div>
        
        <div class="tags-info mt-3">
            <h4>Quiz Tags:</h4>
            <div class="tags-list">
                <?php if (!empty($quiz_tags)): ?>
                    <?php foreach ($quiz_tags as $tag): ?>
                        <span class="badge bg-primary"><?= htmlspecialchars($tag['name']) ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="text-muted">No tags added to this quiz</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (empty($categories)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> No categories found for the selected tags.
            <p class="mt-2">Please ensure that:</p>
            <ul>
                <li>The quiz has tags assigned to it</li>
                <li>Those tags are associated with categories in the Tag Management</li>
            </ul>
            <p>
                <a href="<?= $url('admin/quiz/edit/' . $quiz['id']) ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Edit Quiz Tags
                </a>
                <a href="<?= $url('admin/tag/list') ?>" class="btn btn-sm btn-secondary">
                    <i class="fas fa-tags"></i> Manage Tags
                </a>
            </p>
        </div>
    <?php else: ?>
        <form id="mockConfigForm" action="<?= $url('admin/quiz/configure-mock/' . $quiz['id']) ?>" method="POST">
            <div class="category-grid">
                <?php foreach ($categories as $category): ?>
                    <div class="category-card">
                        <h3><?= htmlspecialchars($category['name']) ?></h3>
                        <div class="input-group">
                            <div class="form-field">
                                <label>Marks Allocation</label>
                                <input type="number"
                                    name="categories[<?= $category['id'] ?>][marks]"
                                    class="marks-input"
                                    min="0"
                                    max="<?= $quiz['total_marks'] ?>"
                                    value="<?= isset($existing_config[$category['id']]) ? $existing_config[$category['id']]['marks_allocated'] : '' ?>"
                                    onchange="validateMarksInput(this)">
                            </div>
                            <div class="form-field">
                                <label>Number of Questions</label>
                                <input type="number"
                                    name="categories[<?= $category['id'] ?>][questions]"
                                    min="0"
                                    value="<?= isset($existing_config[$category['id']]) ? $existing_config[$category['id']]['number_of_questions'] : '' ?>"
                                    onchange="validateQuestions(this)">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" id="submit-btn" class="btn btn-primary mt-3">
                Save Configuration
            </button>
        </form>
    <?php endif; ?>
</div>

<style>
    /* Add to existing styles */
    .category-selection {
        margin-bottom: 30px;
    }

    #categorySelect {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        height: 150px;
    }

    #categorySelect option {
        padding: 5px;
    }

    .mock-config-container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
    }

    .configuration-info {
        margin-bottom: 30px;
    }

    .info-box {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .tags-info {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .tags-list {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 10px;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 20px;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: #eee;
        border-radius: 4px;
        margin-top: 10px;
    }

    .progress {
        height: 100%;
        background: #4CAF50;
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .category-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .input-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 15px;
    }

    .form-field label {
        display: block;
        margin-bottom: 5px;
        color: #666;
    }

    .form-field input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    #submit-btn {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
    }

    #submit-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
</style>

<script>
    function updateMarks() {
        let total = <?= $quiz['total_marks'] ?>;
        let used = 0;

        document.querySelectorAll('.marks-input').forEach(input => {
            used += parseInt(input.value) || 0;
        });

        const remaining = total - used;
        document.getElementById('remaining-marks').textContent = remaining;

        // Disable submit if over total or negative remaining
        document.getElementById('submit-btn').disabled = remaining < 0;

        return remaining; // Return remaining for validation
    }

    function validateMarksInput(input) {
        const currentValue = parseInt(input.value) || 0;
        const oldValue = parseInt(input.dataset.lastValue) || 0;
        const remaining = updateMarks();

        // If remaining is 0 and trying to add more
        if (remaining < 0) {
            alert(`Cannot allocate more marks. Total marks (${<?= $quiz['total_marks'] ?>}) is already allocated.`);
            input.value = oldValue; // Revert to previous value
            updateMarks();
            return false;
        }

        // Store current value for next comparison
        input.dataset.lastValue = input.value;
        return true;
    }

    function validateQuestions(input) {
        if (parseInt(input.value) < 0) {
            input.value = 0;
        }
    }

    document.querySelectorAll('.marks-input').forEach(input => {
        input.dataset.lastValue = input.value; // Store initial value
        input.addEventListener('change', function() {
            validateMarksInput(this);
        });
    });
    
    // Initialize on load
    document.addEventListener('DOMContentLoaded', updateMarks);
</script>