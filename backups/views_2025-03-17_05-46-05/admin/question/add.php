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
<form action="/admin/question/add" method="POST" class="form-group">
    <h2>Add Question</h2>
    <div class="breadcrumb">
        <a href="/admin/question/list">Question</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Create</a>
    </div>
    
    <label for="question_text">Question:</label>
    <input type="text" id="question_text" name="question_text" required>
    <div class="form-group">
        <label>Question Type</label>
        <select name="question_type" required>
            <option selected disabled>--Select question type--</option>
            <option value="mock">Mock Test</option>
            <option value="previous_year">Previous Year</option>
            <option value="quiz"> Quiz</option>
            <option value="real_exam">Real Exam</option>
        </select>
    </div>
    <label for="category_id">Category:</label>
    <select id="category_id" name="category_id" required>
        <option value="">--Select Category--</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="difficulty_level"> Level of difficulty:</label>
        <select id="difficulty_level" name="difficulty_level">
            <option value="0">--Select Level--</option>
            <?php foreach ($levels as $level): ?>
                <option value="<?= $level['id'] ?>"><?= htmlspecialchars($level['level']) ?></option>
            <?php endforeach; ?>
        </select>

    <label for="marks">Marks:</label>
    <input type="number" id="marks" name="marks" min="1" value="1" required>

    <div class="form-group">
        <label for="tags">Tags:</label>
        <select name="tags[]" id="tags" multiple class="form-control">
            <?php foreach ($tags as $tag): ?>
                <option value="<?= $tag['id'] ?>"><?= htmlspecialchars($tag['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <label for="year">Year:</label>
    <input type="text" id="year" name="year" >
    <button class="success mt-5" type="submit">Create</button>
</form>