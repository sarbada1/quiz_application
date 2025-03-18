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
<h1>List Category</h1>
<div class="row">
    <div class="breadcrumb">
        <a href="<?= $url('admin/category/list') ?>">Category</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">List</a>
    </div>
    <div>
        <button class='success mb-5'><a href="<?= $url('admin/category/add') ?>">Add category</a></button>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>SN</th>
            <th>Category Name</th>
            <th>Parent</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1; // Initialize serial number

        foreach ($categories as $category) { ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $category['name'] ?></td>
                <td><?= $category['parent_name'] ?></td>
                <td>
                    <button class="primary"><a href="<?= $url('admin/category/edit/' . $category['id']) ?>">Edit</a></button>
                    <button class="danger"><a href="<?= $url('admin/category/delete/' . $category['id']) ?>" onclick="return confirm('Are you sure to delete?')">Delete</a></button>
                </td>
            </tr>
        <?php }
        ?>
    </tbody>
</table>