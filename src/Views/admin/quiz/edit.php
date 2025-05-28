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
<form action="<?= $url('admin/quiz/edit/' . $quiz['id']) ?>" method="POST" class="form-group">
    <h2>Edit Quiz</h2>
    <div class="breadcrumb">
        <a href="<?= $url('admin/quiz/list') ?>">Quiz</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Edit</a>
    </div>

    <div class="form-group">
        <label>Quiz Type</label>
        <select name="type" required>
            <option value="mock" <?= $quiz['type'] === 'mock' ? 'selected' : '' ?>>Mock Test</option>
            <option value="previous_year" <?= $quiz['type'] === 'previous_year' ? 'selected' : '' ?>>Previous Year</option>
            <option value="real_exam" <?= $quiz['type'] === 'real_exam' ? 'selected' : '' ?>>Real Exam</option>
        </select>
    </div>

    <div class="form-group">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($quiz['title']) ?>" required>
    </div>
    <label for="slug">Slug:</label>
    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($quiz['slug']) ?>" readonly>
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea name="description" id="description"><?= htmlspecialchars($quiz['description']) ?></textarea>
    </div>

    <div class="form-group">
        <label>Tags</label>
        <select name="tags[]" multiple>
            <?php foreach ($tags as $tag): ?>
                <option value="<?= $tag['id'] ?>"
                    <?= in_array($tag['id'], $selectedTags) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tag['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Total Marks</label>
        <input type="number" name="total_marks" value="<?= $quiz['total_marks'] ?>" required>
    </div>

    <div class="form-group">
        <label>Duration (minutes)</label>
        <input type="number" name="duration" value="<?= $quiz['duration'] ?>" required>
    </div>
    <div class="form-group">
        <label>Status</label>
        <select name="status">
            <option selected disabled>--Status--</option>
            <option value="draft" <?= $quiz['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="published" <?= $quiz['status'] === 'published' ? 'selected' : '' ?>>Published</option>
            <option value="archived" <?= $quiz['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
        </select>
    </div>

    <!-- Add exam scheduling fields for real exams -->
    <div class="form-group quiz-type-fields" id="real_exam_fields" style="display: <?= $quiz['type'] === 'real_exam' ? 'block' : 'none' ?>;">
        <h5>Schedule Exam</h5>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Start Date & Time</label>
                    <input type="datetime-local" name="exam_start_time" class="form-control"
                        value="<?= isset($examSession['start_time']) ? date('Y-m-d\TH:i', strtotime($examSession['start_time'])) : '' ?>">
                    <small class="form-text text-muted">When students can begin taking the exam</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>End Date & Time</label>
                    <input type="datetime-local" name="exam_end_time" class="form-control"
                        value="<?= isset($examSession['end_time']) ? date('Y-m-d\TH:i', strtotime($examSession['end_time'])) : '' ?>">
                    <small class="form-text text-muted">When the exam will close</small>
                </div>
            </div>
        </div>
        <div class="form-group mt-2">
    <div class="form-check d-flex align-items-start">
        <input type="checkbox" class="form-check-input mt-1 mr-2" id="schedule_later" 
               <?= !isset($examSession['start_time']) ? 'checked' : '' ?> 
               name="schedule_later">
        <div>
            <label class="form-check-label" for="schedule_later">
                Schedule later
            </label>
            <small class="form-text text-muted">
                Check this if you want to schedule the exam later
            </small>
        </div>
    </div>
</div>

    </div>

    <button type="submit" class="btn btn-primary">Update Quiz</button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide fields based on quiz type selection
        const typeSelect = document.querySelector('select[name="type"]');
        const realExamFields = document.getElementById('real_exam_fields');

        typeSelect.addEventListener('change', function() {
            if (this.value === 'real_exam') {
                realExamFields.style.display = 'block';
            } else {
                realExamFields.style.display = 'none';
            }
        });

        // Generate slug from title
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');

        titleInput.addEventListener('input', function() {
            slugInput.value = this.value.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-');
        });

        // Toggle date fields when "Schedule later" is checked
        const scheduleLaterCheckbox = document.getElementById('schedule_later');
        const dateTimeInputs = document.querySelectorAll('input[type="datetime-local"]');

        scheduleLaterCheckbox.addEventListener('change', function() {
            dateTimeInputs.forEach(input => {
                input.disabled = this.checked;
            });
        });

        // Initialize the state
        dateTimeInputs.forEach(input => {
            input.disabled = scheduleLaterCheckbox.checked;
        });
    });
</script>