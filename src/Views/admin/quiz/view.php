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
<h1>List Quizzes</h1>
<div class="row">
    <div class="breadcrumb">
        <a href="/admin/quiz/list">Quizz</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">List</a>
    </div>
    <div>
        <button class='success mb-5'><a href='/admin/quiz/add'>Add Quiz</a></button>
    </div>
</div>
<table>
    <thead>
        <tr>
            <th>SN</th>
            <th>Title</th>
            <th>Category</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($quizzes as $quiz): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?= $quiz['title'] ?></td>
                <td><?= $quiz['name'] ?></td>
                <td>
                    <button class="primary"> <a href="/admin/quiz/edit/<?= $quiz['id'] ?>">Edit</a></button>
                    <button class="danger"> <a href="/admin/quiz/delete/<?= $quiz['id'] ?>" onclick="return confirm('Are you sure to delete?')">Delete</a></button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>