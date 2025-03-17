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
<form action="/admin/question/edit/<?= $question['id'] ?>" method="POST" class="form-group">
    <h2>Edit Question</h2>
    <div class="breadcrumb">
        <a href="/admin/question/list">Question</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Edit</a>
    </div>
    
    <label for="question_text">Question:</label>
    <input type="text" id="question_text" name="question_text" value="<?= htmlspecialchars($question['question_text']) ?>" required>

    <div class="form-group">
    <label>Question Type</label>
    <select name="question_type" required>
        <option disabled>--Select question type--</option>
        <option value="mock" <?= $question['question_type'] == 'mock' ? 'selected' : '' ?>>Mock Test</option>
        <option value="previous_year" <?= $question['question_type'] == 'previous_year' ? 'selected' : '' ?>>Previous Year</option>
        <option value="quiz" <?= $question['question_type'] == 'quiz' ? 'selected' : '' ?>>Quiz</option>
        <option value="real_exam" <?= $question['question_type'] == 'real_exam' ? 'selected' : '' ?>>Real Exam</option>
    </select>
</div>



    <label for="category_id">Category:</label>
    <select id="category_id" name="category_id" required>
        <option value="">--Select Category--</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>" <?= $category['id'] == $question['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="difficulty_level">Level of difficulty:</label>
    <select id="difficulty_level" name="difficulty_level">
        <option value="0">--Select Level--</option>
        <?php foreach ($levels as $level): ?>
            <option value="<?= $level['id'] ?>" <?= $level['id'] == $question['difficulty_level'] ? 'selected' : '' ?>><?= htmlspecialchars($level['level']) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="marks">Marks:</label>
    <input type="number" id="marks" name="marks" min="1" value="<?= htmlspecialchars($question['marks']) ?>" required>

    <div class="form-group">
        <label for="tags">Tags:</label>
        <select name="tags[]" id="tags" multiple class="form-control">
            <?php foreach ($tags as $tag): ?>
                <option value="<?= $tag['id'] ?>" <?= in_array($tag['id'], $questionTags) ? 'selected' : '' ?>><?= htmlspecialchars($tag['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <label for="year">Year:</label>
    <input type="text" id="year" name="year" value="<?= htmlspecialchars($question['year']) ?>">

    <button class="success mt-5" type="submit">Save changes</button>
</form>