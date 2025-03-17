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
<h1>Mock Test of : <?= $program['name'] ?></h1>
<div class="row">
    <div class="breadcrumb">
        <a href="<?= $url('admin/program/list') ?>">Test</a>
        <i class="fas fa-chevron-right"></i>
        <a href="<?= $url('admin/mocktest/list/<?= $program[') ?>"id'] ?>" style="margin-left: 7px;cursor:default">MockTest</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">List</a>
    </div>
    <div>
        <button class='success mb-5'><a href="<?= $url('admin/mocktest/add/<?= $program[') ?>"id'] ?>'>Add Mock Test</a></button>
    </div>
</div>
<table>
    <thead>
        <tr>
            <th>S.N</th>
            <th>Name</th>
            <th>Time (in seconds)</th>
            <th>No of Student</th>
            <th>Question</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($mocktests as $answer): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $answer['name'] ?></td>
                <td><?= $answer['time'] ?> seconds</td>
                <td><?= $answer['no_of_student'] ?> </td>
                <td>
                    <button class="info"> <a href="<?= $url('admin/mocktestquestion/add/<?= $answer[') ?>"id'] ?>">Add</a></button>
                    <button class="warning"> <a href="<?= $url('admin/mocktestquestion/list/<?= $answer[') ?>"id'] ?>">View</a></button>
                </td>
                <td>
                    <button class="primary"> <a href="<?= $url('admin/mocktest/edit/<?= $answer[') ?>"id'] ?>">Edit</a></button>
                    <button class="danger"> <a href="<?= $url('admin/mocktest/delete/<?= $answer[') ?>"id'] ?>" onclick="return confirm('Are you sure to delete?')">Delete</a></button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>