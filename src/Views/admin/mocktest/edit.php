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
<h1>Edit Mock Test: <?= htmlspecialchars($mocktest['name']) ?></h1>
<div class="row">
    <div class="breadcrumb">
        <a href="<?= $url('admin/program/list') ?>">Test</a>
        <i class="fas fa-chevron-right"></i>
        <a href="<?= $url('admin/mocktest/list/<?= $mocktest[') ?>"program_id'] ?>" style="margin-left: 7px;cursor:default">MockTest</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Edit</a>
    </div>
</div>
<form method="post" action="" class="form-group">
    <div class="form-group">
        <label for="title">Name</label>
        <input type="text" class="form-control" id="title" name="name" value="<?= htmlspecialchars($mocktest['name']) ?>" required>
    </div>
    <div class="form-group">
        <label for="slug">Slug</label>
        <input type="text" class="form-control" id="slug" value="<?= htmlspecialchars($mocktest['slug']) ?>" name="slug">
    </div>
    <div class="form-group">
        <label for="time">Time required (in seconds)</label>
        <input type="number" class="form-control" id="time" name="time" value="<?= htmlspecialchars($mocktest['time']) ?>" required>
    </div>
    <div class="form-group">
        <label for="date">Exam Date</label>
        <input type="date" class="form-control" id="date" value="<?= htmlspecialchars($mocktest['date']) ?>" name="date">
    </div>
    <div class="form-group">
        <label for="exam_time">Exam time</label>
        <input type="time" class="form-control" id="exam_time" value="<?= htmlspecialchars($mocktest['exam_time']) ?>" name="exam_time">
    </div>
    <div class="form-group">
        <label for="no_of_student">No of Students</label>
        <input type="number" class="form-control" id="no_of_student" name="no_of_student" value="<?= htmlspecialchars($mocktest['no_of_student']) ?>" >
    </div>

    <button class="success mt-5" type="submit">Update</button>
</form>