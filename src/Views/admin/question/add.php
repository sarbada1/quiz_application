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
    <label for="title">Question:</label>
    <input type="text" id="title" name="question_text" required>
   
    <label for="question_type"> Question Type:</label>
    <select id="question_type" name="question_type">
        <option value="0">--Select question type--</option>
        <?php foreach ($questionTypes as $questionType): ?>
            <option value="<?= $questionType['id'] ?>"><?= htmlspecialchars($questionType['type']) ?></option>
        <?php endforeach; ?>
    </select>
    <label for="quiz_id"> Quizz:</label>
    <select id="quiz_id" name="quiz_id">
        <option value="0">--Select quiz--</option>
        <?php foreach ($quizModels as $quizModel): ?>
            <option value="<?= $quizModel['id'] ?>"><?= htmlspecialchars($quizModel['title']) ?></option>
        <?php endforeach; ?>
    </select>
    
    <button class="success mt-5" type="submit">Create</button>
</form>