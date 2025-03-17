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
<h1>List Student</h1>
<div class="row">
    <div class="breadcrumb">
        <a href="/admin/student/list">Student</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">List</a>
    </div>
   
</div>
<table>
    <thead>
        <tr>
            <th>SN</th>
            <th>Username</th>
            <th>Phone</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($teachers as $teacher): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?= $teacher['username'] ?></td>
                <td><?= $teacher['phone'] ?></td>
              
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>