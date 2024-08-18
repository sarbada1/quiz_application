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
<form method="POST">
    <h1>Edit Category</h1>
    <div class="row">
        <div class="breadcrumb">
            <a href="/admin/category/list">Category</a>
            <i class="fas fa-chevron-right"></i>
            <a href="#" style="margin-left: 7px;cursor:default">Edit</a>
        </div>
        <div>
            <button class='danger mb-5'><a href='/admin/category/delete/<?=$category['id']?>' onclick="return confirm('Are you sure to delete?')">Delete</a></button>
        </div>
    </div>
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>

    <label for="parent_id">Parent Category:</label>
    <select id="parent_id" name="parent_id">
        <option value="0">Top Level</option>
        <?php foreach ($categories as $cat): ?>
            <?php if ($cat['id'] != $category['id']): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $category['parent_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="warning mt-5">Save changes</button>
</form>