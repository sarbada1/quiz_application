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
<h1>List Teacher</h1>
<div class="row">
    <div class="breadcrumb">
        <a href="/admin/teacher/list">Teacher</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">List</a>
    </div>
    <div>
        <button class='success mb-5'><a href='/admin/teacher/add'>Add teacher</a></button>
    </div>
</div>
<table>
    <thead>
        <tr>
            <th>SN</th>
            <th>Username</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($teachers as $teacher): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?= $teacher['username'] ?></td>
                <td><?= $teacher['email'] ?></td>
                <td>
                    <button class="primary"> <a href="/admin/teacher/edit/<?= $teacher['id'] ?>">Edit</a></button>
                    <button class="danger"> <a href="/admin/teacher/delete/<?= $teacher['id'] ?>" onclick="return confirm('Are you sure to delete?')">Delete</a></button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>