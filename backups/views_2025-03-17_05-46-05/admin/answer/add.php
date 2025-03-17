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
<h1>Add Answer for Question: <?= $question['question_text'] ?></h1>
<div class="row">
    <div class="breadcrumb">
        <a href="/admin/question/list">Question</a>
        <i class="fas fa-chevron-right"></i>
        <a href="/admin/answer/list/<?= $question['id'] ?>" style="margin-left: 7px;">Answers</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Create</a>
    </div>
</div>
<form method="post" action="/admin/answer/add/<?= $question['id'] ?>" class="form-group">
    <div class="form-group">
        <label for="answer">Answer</label>
        <input type="text" class="form-control" id="answer" name="answer" required>
    </div>
    <div class="form-group">
        <label for="reason">Reason</label>
        <textarea class="form-control" id="reason" name="reason"></textarea>
    </div>
    <div class="form-group">
        <label for="isCorrect">Is Correct?</label>
        <input type="checkbox"  id="isCorrect" name="isCorrect" value="1">

    </div>
    <button class="success mt-5" type="submit">Create</button>
</form>