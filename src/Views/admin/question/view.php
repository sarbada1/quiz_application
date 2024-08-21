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
<h1>List Question</h1>
<div class="row">
    <div class="breadcrumb">
        <a href="/admin/question/list">Question</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">List</a>
    </div>
    <div>
        <button class='success mb-5'><a href='/admin/question/add'>Add Question</a></button>
    </div>
</div>
<table>
    <thead>
        <tr>
            <th>SN</th>
            <th>Question</th>
            <th>Quiz</th>
            <th>Type</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($questions as $question): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?= $question['question_text'] ?></td>
                <td><?= $question['title'] ?></td>
                <td><?= $question['type'] ?></td>
                <td>
                    <button class="primary"> <a href="/admin/question/edit/<?= $question['id'] ?>">Edit</a></button>
                    <button class="danger"> <a href="/admin/question/delete/<?= $question['id'] ?>" onclick="return confirm('Are you sure to delete?')">Delete</a></button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>