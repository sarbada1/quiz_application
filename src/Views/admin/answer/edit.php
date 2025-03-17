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

<form method="post" action="<?= $url('admin/answer/edit/<?= $answer[') ?>"id'] ?>">
    <h1>Edit Answer for Question: <?= $question['question_text'] ?></h1>
    <div class="row">
        <div class="breadcrumb">
            <a href="<?= $url('admin/question/list') ?>">Question</a>
            <i class="fas fa-chevron-right"></i>
            <a href="<?= $url('admin/answer/list/<?= $question[') ?>"id'] ?>" style="margin-left: 7px;">Answers</a>
            <i class="fas fa-chevron-right"></i>
            <a href="#" style="margin-left: 7px;cursor:default">Edit</a>
        </div>
        <div>
            <button class='danger mb-5'><a href="<?= $url('admin/answer/delete/<?= $answer[') ?>"question_id'] ?>' onclick="return confirm('Are you sure to delete?')">Delete</a></button>
        </div>
    </div>
    <div class="form-group">
        <label for="answer">Answer</label>
        <input type="text" class="form-control" id="answer" name="answer" value="<?= $answer['answer'] ?>" required>
    </div>
    <div class="form-group">
        <label for="reason">Reason</label>
        <textarea class="form-control" id="reason" name="reason"><?= $answer['reason'] ?></textarea>
    </div>
    <div class="form-group">
        <label for="isCorrect">Is Correct?</label>
        <input type="checkbox" class="form-control" id="isCorrect" name="isCorrect" value="1" <?= $answer['isCorrect'] ? 'checked' : '' ?>>
    </div>
    <button class="success mt-5" type="submit">Save changes</button>
</form>