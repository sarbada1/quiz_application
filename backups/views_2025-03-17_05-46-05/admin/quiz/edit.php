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
<form action="/admin/quiz/edit/<?= $quiz['id'] ?>" method="POST" class="form-group">
    <h2>Edit Quiz</h2>
    <div class="breadcrumb">
        <a href="/admin/quiz/list">Quiz</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Edit</a>
    </div>

    <div class="form-group">
        <label>Quiz Type</label>
        <select name="type" required>
            <option value="mock" <?= $quiz['type'] === 'mock' ? 'selected' : '' ?>>Mock Test</option>
            <option value="previous_year" <?= $quiz['type'] === 'previous_year' ? 'selected' : '' ?>>Previous Year</option>
            <option value="quiz" <?= $quiz['type'] === 'quiz' ? 'selected' : '' ?>>Quiz</option>
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
        <label>Categories</label>
        <select name="categories[]" multiple required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" 
                    <?= in_array($category['id'], $selectedCategories) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
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

    <button type="submit" class="btn btn-primary">Update Quiz</button>
</form>