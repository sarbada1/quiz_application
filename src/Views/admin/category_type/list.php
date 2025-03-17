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
<h1>Category Types</h1>
<div class="row">
    <div class="breadcrumb">
        <a href="<?= $url('admin/category-type/list') ?>">Category Types</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">List</a>
    </div>
    <div>
        <button class='success mb-5'><a href="<?= $url('admin/category-type/add') ?>">Add Category Type</a></button>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($types as $type): ?>
            <tr>
                <td><?= htmlspecialchars($type['id']) ?></td>
                <td><?= htmlspecialchars($type['name']) ?></td>  
                <td>
                    <button class="primary"><a href="<?= $url('admin/category-type/edit/<?= $type[') ?>"id'] ?>">Edit</a></button>
                    <button class="danger"><a href="<?= $url('admin/category-type/delete/<?= $type[') ?>"id'] ?>" onclick="return confirm('Are you sure?')">Delete</a></button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>