<?php if (isset($_SESSION['message']) && $_SESSION['message']): ?>
<div id="success-alert" class="alert alert-<?=$_SESSION['status']?>" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button> 
    <?= $_SESSION['message'] ?>   
</div>
<?php 
 unset($_SESSION['message']);
 unset($_SESSION['status']);
endif; ?>
<form action="<?= $url('admin/level/add') ?>" method="POST" class="form-group">
<h2>Add Level</h2>
<div class="breadcrumb">
        <a href="<?= $url('admin/level/list') ?>">Level</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Create</a>
    </div>
    <label for="level">Level:</label>
    <input type="text" id="level" name="level" >
    
    <button class="success mt-5" type="submit">Create</button>
</form>