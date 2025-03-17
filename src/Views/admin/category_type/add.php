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
<h1>Add Category Type</h1>
<div class="breadcrumb">
    <a href="<?= $url('admin/category-type/list') ?>">Category Types</a>
    <i class="fas fa-chevron-right"></i>
    <a href="#" style="margin-left: 7px;cursor:default">Add</a>
</div>

<form method="POST" action="<?= $url('admin/category-type/add') ?>">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
    </div>
    
    <button type="submit" class="success">Add Category Type</button>
</form>