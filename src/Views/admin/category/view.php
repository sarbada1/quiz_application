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

<!-- DataTable for Categories -->
<table id="categoryTable" class="display">
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
                <td><?= htmlspecialchars($category['name']) ?></td>
                <td><?= htmlspecialchars($category['parent_name']) ?></td>
                <td class="buttons">
                    <button class="primary action-btn"><a href="<?= $url('admin/category/edit/' . $category['id']) ?>">Edit</a></button>
                    <button class="danger action-btn"><a href="<?= $url('admin/category/delete/' . $category['id']) ?>" onclick="return confirm('Are you sure you want to delete this category?')">Delete</a></button>
                </td>
            </tr>
        <?php }
        ?>
    </tbody>
</table>

<!-- Add required CSS for DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- Add required JS for DataTables -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script>
    // Initialize DataTable with options
    $(document).ready(function() {
        $('#categoryTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            columnDefs: [
                { orderable: false, targets: 3 } // Disable sorting on action column
            ],
            language: {
                search: "Search categories:",
                lengthMenu: "Show _MENU_ categories per page",
                info: "Showing _START_ to _END_ of _TOTAL_ categories",
                infoEmpty: "No categories found",
                emptyTable: "No categories available"
            }
        });
    });
</script>

<style>
    /* Custom styles for DataTables */
    #categoryTable {
        width: 100%;
        margin-top: 20px;
    }
    
    #categoryTable_filter input {
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-left: 10px;
    }
    
    #categoryTable_length select {
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .buttons{
        display: flex;
    }
    
 
    
    .action-btn a {
        text-decoration: none;
        color: white;
        display: block;
    }
    
    #categoryTable thead th {
        background-color: #f5f5f5;
        padding: 10px;
    }
    
    #categoryTable tbody tr:hover {
        background-color: #f9f9f9;
    }
    
    #categoryTable_paginate .paginate_button {
        padding: 5px 10px;
        margin: 2px;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
    }
    
    #categoryTable_paginate .paginate_button.current {
        background-color: #4CAF50;
        color: white;
        border-color: #4CAF50;
    }
    
    #categoryTable_info, #categoryTable_paginate {
        margin-top: 15px;
    }
</style>