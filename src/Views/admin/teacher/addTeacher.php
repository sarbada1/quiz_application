<?php
session_start();
?>

<form method="POST" action="/admin/teacher/add">
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
    <h1>Add Teacher</h1>
    <div class="breadcrumb">
        <a href="/admin/teacher/list">Teacher</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Create</a>
    </div>
    <label for="username">Username:</label>
    <input type="text" id="username" name="username">

    <label for="email">Email:</label>
    <input type="email" id="email" name="email">

    <label for="password">Password:</label> 
    <input type="password" id="password" name="password"> 

    <label for="cpassword">Confirm Password:</label>
    <input type="password" id="cpassword" name="cpassword">

    <button class="success"
        type="submit">Create</button>
</form>