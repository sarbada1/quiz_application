<?php if (isset($_SESSION['message'])): ?>
    <div id="alert" class="alert alert-<?= $_SESSION['status'] ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= $_SESSION['message'] ?>
    </div>
    <?php
    // Unset session messages after displaying
    unset($_SESSION['message']);
    unset($_SESSION['status']);
    ?>
<?php endif; ?>

<h1>Edit Question for Mock Test</h1>
<div class="row">
    <div class="breadcrumb">
        <a href="/admin/mocktest/list/<?= $mockTest['program_id'] ?>">MockTest</a>
        <i class="fas fa-chevron-right"></i>
        <a href="/admin/mocktestquestion/list/<?= $mockTest['id'] ?>" style="margin-left: 7px">Questions</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Edit</a>
    </div>
</div>

<form method="post" action="" class="form-group">
<table>
    <tbody>
        <?php
        $i = 1;
        foreach ($questions as $question) {
            // If the question ID is in the $existingQuestions array, mark it as checked
            $checked = in_array($question['id'], $existingQuestions) ? 'checked' : '';
        ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><label for="question_<?= $question['id'] ?>"><?= $question['question_text'] ?></label></td>
                <td><input type="checkbox" name="questions[]" value="<?= $question['id'] ?>" id="question_<?= $question['id'] ?>" <?= $checked ?>></td>
            </tr>
        <?php } ?>
    </tbody>
</table>


    <button class="success mt-5" type="submit">Update</button>
</form>
