<?php
session_start();
?>

<form method="POST" action="<?= $url('admin/teacher/edit/<?= $teacher[') ?>" id'] ?>" class="form-group">
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
    <h1>Edit Teacher</h1>
    <div class="row">
        <div class="breadcrumb">
            <a href="<?= $url('admin/teacher/list') ?>">Teacher</a>
            <i class="fas fa-chevron-right"></i>
            <a href="#" style="margin-left: 7px;cursor:default">Edit</a>
        </div>
        <div>
        <button class='danger mb-5'><a href="<?= $url('admin/teacher/delete/' . $teacher['id']) ?>" onclick="return confirm('Are you sure to delete?')">Delete</a></button>        </div>
    </div>
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" value="<?= htmlspecialchars($teacher['username']) ?>" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($teacher['email']) ?>" required>

    <label for="password">Password (leave blank to keep current password):</label>
    <input type="password" id="password" name="password">

    <label for="cpassword">Confirm Password:</label>
    <input type="password" id="cpassword" name="cpassword">

    <div class="flex w-1">
        <button class="success mt-5" type="submit">Save changes</button>
    </div>
</form>