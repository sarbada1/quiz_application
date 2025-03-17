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
<form action="/admin/quiz/add" method="POST" class="form-group">
    <h2>Add Quiz</h2>
    <div class="breadcrumb">
        <a href="/admin/quiz/list">Quiz</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Create</a>
    </div>
    <div class="form-group">
        <label>Quiz Type</label>
        <select name="type" required>
            <option selected disabled>--Select quiz type--</option>
            <option value="mock">Mock Test</option>
            <option value="previous_year">Previous Year</option>
            <option value="quiz"> Quiz</option>
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
        <label>Categories</label>
        <select name="categories[]" multiple required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple categories</small>
    </div>
    <div class="form-group">
        <label>Tags</label>
        <select name="tags[]" multiple >
            <?php foreach ($tags as $tag): ?>
                <option value="<?= $tag['id'] ?>"><?= htmlspecialchars($tag['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple categories</small>

    </div>
    <div class="form-group">
        <label>Total Marks</label>
        <input type="number" min="0" name="total_marks" >
    </div>

    <div class="form-group">
        <label>Duration (minutes)</label>
        <input type="number" min="0" name="duration" >
    </div>
    <div class="form-group">
        <label>Status</label>
        <select name="status" >
            <option selected disabled>--Status--</option>
            <option value="draft">Draft</option>
            <option value="published">Published </option>
            <option value="archived"> Archived</option>
        </select>
    </div>

    <button class="success mt-5" type="submit">Create</button>
</form>