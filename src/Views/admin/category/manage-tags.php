<?php if (isset($_SESSION['message'])): ?>
    <div id="alert" class="alert alert-<?= $_SESSION['status'] ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= $_SESSION['message'] ?>
    </div>
    <?php unset($_SESSION['message']); unset($_SESSION['status']); ?>
<?php endif; ?>

<h2>Manage Tags for Category: <?= htmlspecialchars($category['name']) ?></h2>

<div class="row">
    <div class="breadcrumb">
        <a href="<?= $url('admin/category/list') ?>">Categories</a>
        <i class="fas fa-chevron-right"></i>
        <a href="<?= $url('admin/category/edit/' . $category['id']) ?>"><?= htmlspecialchars($category['name']) ?></a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Manage Tags</a>
    </div>
</div>

<form method="POST" action="<?= $url('admin/category/manage-tags/' . $category['id']) ?>">
    <div class="form-group">
        <label for="tags">Select Tags:</label>
        <select class="form-control" id="tags" name="tags[]" multiple size="10">
            <?php foreach ($tags as $tag): ?>
                <option value="<?= $tag['id'] ?>" 
                        <?= in_array($tag['id'], $associatedTagIds) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tag['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">Hold Ctrl (or Cmd) to select multiple tags</small>
    </div>
    
    <button type="submit" class="btn btn-primary">Save Tag Associations</button>
    <a href="<?= $url('admin/category/edit/' . $category['id']) ?>" class="btn btn-secondary">Cancel</a>
</form>

<script>
    $(document).ready(function() {
        // Use Select2 for better UX (if available)
        if ($.fn.select2) {
            $('#tags').select2({
                placeholder: 'Select tags to associate with this category',
                width: '100%'
            });
        }
    });
</script>