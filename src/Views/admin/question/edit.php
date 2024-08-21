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
<form method="POST" class="form-group">
    <h2>Edit Question</h2>
    <div class="row">
        <div class="breadcrumb">
            <a href="/admin/question/list">Question</a>
            <i class="fas fa-chevron-right"></i>
            <a href="#" style="margin-left: 7px;cursor:default">Edit</a>
        </div>
        <div>
            <button class='danger mb-5'><a href='/admin/question/delete/<?= $category['id'] ?>' onclick="return confirm('Are you sure to delete?')">Delete</a></button>
        </div>
    </div>
    <label for="title">Question:</label>
    <input type="text" id="title" name="question_text" value="<?= htmlspecialchars($category['question_text']) ?>" required>

    <label for="question_type"> Question Type:</label>
    <select id="question_type" name="question_type">
        <option value="0" selected disabled>--Select question type--</option>
        <?php foreach ($questionTypes as $questionType): ?>
            <option value="<?= $questionType['id'] ?>" <?= $questionType['id'] == $category['question_type'] ? 'selected' : '' ?>><?= htmlspecialchars($questionType['type']) ?></option>
        <?php endforeach; ?>
    </select>
    <label for="quiz_id"> Quizz:</label>
    <select id="quiz_id" name="quiz_id">
        <option value="0" selected disabled>--Select quiz--</option>
        <?php foreach ($quizModels as $quizModel): ?>
            <option value="<?= $quizModel['id'] ?>" <?= $quizModel['id'] == $category['quiz_id'] ? 'selected' : '' ?>><?= htmlspecialchars($quizModel['title']) ?></option>
        <?php endforeach; ?>
    </select>

    <button class="success mt-5" type="submit">Save changes</button>
</form>