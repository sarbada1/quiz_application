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
    </div>

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
    function loadCategoryForms() {
        const selectedCategories = Array.from(document.getElementById('categorySelect').selectedOptions)
            .map(option => ({
                id: option.value,
                name: option.text
            }));

        if (selectedCategories.length === 0) {
            alert('Please select at least one category');
            return;
        }

        const container = document.getElementById('categoryFormsContainer');
        container.innerHTML = '';

        selectedCategories.forEach(category => {
            container.innerHTML += `
            <div class="category-card">
                <h3>${category.name}</h3>
                <div class="input-group">
                    <div class="form-field">
                        <label>Marks Allocation</label>
                        <input type="number" 
                               name="categories[${category.id}][marks]" 
                               class="marks-input"
                               min="0" 
                               max="<?= $quiz['total_marks'] ?>"
                               onchange="updateMarks()">
                    </div>
                    <div class="form-field">
                        <label>Number of Questions</label>
                        <input type="number" 
                               name="categories[${category.id}][questions]"
                               min="0"
                               onchange="validateQuestions(this)">
                    </div>
                </div>
            </div>
        `;
        });

        document.getElementById('mockConfigForm').style.display = 'block';
    }

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

    function submitMockConfig(event) {
        event.preventDefault();

        const form = document.getElementById('mockConfigForm');
        const formData = new FormData(form);

        fetch('<?= $url('admin/quiz/update-config') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const alertContainer = document.getElementById('alert-container');

                alertContainer.innerHTML = `
            <div class="alert alert-${data.success ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

                if (data.success) {
                    // Update form values if needed
                    updateFormValues(data.config);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('alert-container').innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                An error occurred. Please try again.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
            });

        return false;
    }

    function updateFormValues(config) {
        // Update any form values that need to be refreshed
        if (config.remaining_marks) {
            document.getElementById('remaining-marks').textContent = config.remaining_marks;
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