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
<form action="<?= $url('admin/category/add') ?>" method="POST" class="form-group">
<h2>Add New Category</h2>
<div class="breadcrumb">
        <a href="<?= $url('admin/category/list') ?>">Category</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Create</a>
    </div>
    <label for="name">Name:</label>
    <input type="text" id="title" name="name" >

    <label for="slug">Slug:</label>
    <input type="text" id="slug" name="slug" readonly>
    
    <label for="parent_id">Parent Category:</label>
    <select id="parent_id" name="parent_id">
        <option value="0">None (Top Level)</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>">
                <?= htmlspecialchars($category['name']) ?> 
                <?= $category['parent_name'] ? '(' . htmlspecialchars($category['parent_name']) . ')' : '(Top Level)' ?>
            </option>
        <?php endforeach; ?>
    </select>



    
    <button class="success mt-5" type="submit">Create</button>
</form>