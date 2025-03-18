<?php if (isset($_SESSION['message']) && $_SESSION['message']): ?>
    <div id="success-alert" class="alert alert-<?= $_SESSION['status'] ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= $_SESSION['message'] ?>
    </div>
<?php
    unset($_SESSION['message']);
    unset($_SESSION['status']);
endif; ?>
<form method="POST" class="form-group">
    <h2>Edit Level</h2>
    <div class="row">
        <div class="breadcrumb">
            <a href="<?= $url('admin/level/list') ?>">Level</a>
            <i class="fas fa-chevron-right"></i>
            <a href="#" style="margin-left: 7px;cursor:default">Edit</a>
        </div>
        <div>
        <button class='danger mb-5'><a href="<?= $url('admin/level/delete/' . $category['id']) ?>" onclick="return confirm('Are you sure to delete?')">Delete</a></button>
        </div>
    </div>
    <label for="level">Type:</label>
    <input type="text" id="level" name="level" value="<?= htmlspecialchars($category['level']) ?>" >

   

    <button class="success mt-5" type="submit">Save changes</button>
</form>