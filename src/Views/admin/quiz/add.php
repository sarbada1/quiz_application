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
<form action="<?= $url('admin/quiz/add') ?>" method="POST" class="form-group">
    <h2>Add Quiz</h2>
    <div class="breadcrumb">
        <a href="<?= $url('admin/quiz/list') ?>">Quiz</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Create</a>
    </div>
    <div class="form-group">
        <label>Quiz Type</label>
        <select name="type" required>
            <option selected disabled>--Select quiz type--</option>
            <option value="mock">Mock Test</option>
            <option value="previous_year">Previous Year</option>
            <option value="real_exam">Real Exam</option>
        </select>
    </div>
    <label for="title">Title:</label>
    <input type="text" id="title" name="title">
    <label for="slug">Slug:</label>
    <input type="text" id="slug" name="slug" readonly>
    <label for="description">Description:</label>
    <textarea name="description" id="description"></textarea>

    <div class="form-group">
        <label>Tags</label>
        <select name="tags[]" multiple>
            <?php foreach ($tags as $tag): ?>
                <option value="<?= $tag['id'] ?>"><?= htmlspecialchars($tag['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple categories</small>

    </div>
    <div class="form-group">
        <label>Total Marks</label>
        <input type="number" min="0" name="total_marks">
    </div>

    <div class="form-group">
        <label>Duration (minutes)</label>
        <input type="number" min="0" name="duration">
    </div>
    <div class="form-group">
        <label>Status</label>
        <select name="status">
            <option selected disabled>--Status--</option>
            <option value="draft">Draft</option>
            <option value="published">Published </option>
            <option value="archived"> Archived</option>
        </select>
    </div>
    <div class="form-group quiz-type-fields" id="real_exam_fields" style="display: none;">
        <h5>Schedule Exam</h5>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Start Date & Time</label>
                    <input type="datetime-local" name="exam_start_time" class="form-control">
                    <small class="form-text text-muted">When students can begin taking the exam</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>End Date & Time</label>
                    <input type="datetime-local" name="exam_end_time" class="form-control">
                    <small class="form-text text-muted">When the exam will close</small>
                </div>
            </div>
        </div>

        <div class="form-group mt-2">
            <input type="checkbox" class="form-check-input" id="schedule_later" name="schedule_later"
                style="margin-top: 5px; margin-right: 8px;">
            <div>
                <label class="form-check-label" for="schedule_later" style="margin-bottom: 0;">
                    Schedule later
                </label>
                <small class="form-text text-muted" style="display: block;">
                    Check this if you want to schedule the exam later
                </small>
            </div>
        </div>
    </div>
    <button class="success mt-5" type="submit">Create</button>
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

        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');

        titleInput.addEventListener('input', function() {
            slugInput.value = this.value.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-');
        });
    });
</script>