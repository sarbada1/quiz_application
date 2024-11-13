<!-- list.php -->
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
<div class="container">
    <h2>Test List</h2>
 
    <div class="row">
        <div class="breadcrumb">
        <a href="/admin/program/list">Test</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">List</a>
        </div>
        <div>
            <button class='success mb-5'><a href='/admin/program/add'>Add New Test</a></button>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>S.N</th>
                <th>Name</th>
                <th>Category</th>
                <th>Mock Test</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i=1;
            foreach ($programs as $program): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($program['name']) ?></td>
                    <td><?= htmlspecialchars($program['cname']) ?></td>
                    <td>
                      <button class="info">  <a href="/admin/mocktest/add/<?= $program['id'] ?>" >Add</a></button>
                      <button class="warning"> <a href="/admin/mocktest/list/<?= $program['id'] ?>" >View</a></button> 
                    </td>
                    <td>
                      <button class="primary">  <a href="/admin/program/edit/<?= $program['id'] ?>" >Edit</a></button>
                      <button class="danger"> <a href="/admin/program/delete/<?= $program['id'] ?>" onclick="return confirm('Are you sure you want to delete this program?')">Delete</a></button> 
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>