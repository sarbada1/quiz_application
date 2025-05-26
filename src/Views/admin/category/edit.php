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
<div class="row mb-3">
    <div class="col">
        <h1>Edit Category</h1>
    </div>
    <div class="col-auto">
        <a href="<?= $url('admin/category/delete/' . $category['id']) ?>" 
           class="btn btn-danger" 
           onclick="return confirm('Are you sure you want to delete this category?')">
            <i class="fas fa-trash"></i> Delete
        </a>
    </div>
</div>

<div class="breadcrumb mb-4">
    <a href="<?= $url('admin/category/list') ?>">Category</a>
    <i class="fas fa-chevron-right mx-2"></i>
    <span>Edit</span>
</div>

<div class="row mb-4">
    <div class="col">
        <a href="<?= $url('admin/category/manage-tags/' . $category['id']) ?>" class="btn btn-info">
            <i class="fas fa-tags"></i> Manage Tags
        </a>
    </div>
</div>

<form method="POST" class="form-group">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
    </div>

    <div class="form-group">
        <label for="slug">Slug:</label>
        <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlspecialchars($category['slug']) ?>" readonly>
        <small class="text-muted">Slug is automatically generated and cannot be edited</small>
    </div>

    <div class="form-group">
        <label for="parent_id">Parent Category:</label>
        <select class="form-control" id="parent_id" name="parent_id">
            <option value="0">Top Level</option>
            <?php foreach ($categories as $cat): ?>
                <?php if ($cat['id'] != $category['id']): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $category['parent_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>





    <button type="submit" class="btn btn-warning mt-4">
        <i class="fas fa-save"></i> Save Changes
    </button>
</form>

<script>
// Add a script to confirm form changes before leaving the page
let formChanged = false;
document.querySelector('form').addEventListener('change', function() {
    formChanged = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
    }
});

// Reset the flag when the form is submitted
document.querySelector('form').addEventListener('submit', function() {
    formChanged = false;
});
</script>