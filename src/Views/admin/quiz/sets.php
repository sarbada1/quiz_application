<div class="container">
    <h2>Sets for Quiz: <?= htmlspecialchars($quiz['title']) ?></h2>
    
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

    <form action="<?= $url('admin/quiz/<?= $quiz[') ?>"id'] ?>/sets/create" method="POST" class="mb-4">
        <div class="form-group">
            <label>Set Name:</label>
            <input type="text" name="set_name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Status:</label>
            <select name="status" class="form-control" required>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Create Set</button>
    </form>

    <table class="table mt-5">
        <thead>
            <tr>
                <th>Set Name</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sets as $set): ?>
            <tr>
                <td><?= htmlspecialchars($set['set_name']) ?></td>
                <td>
    <select class="form-control status-select" data-set-id="<?= $set['id'] ?>">
        <option value="draft" <?= $set['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= $set['status'] === 'published' ? 'selected' : '' ?>>Published</option>
    </select>
</td>
<td><?= htmlspecialchars($set['created_at']) ?></td>
<td>
    <a href="<?= $url('admin/quiz/sets/<?= $set[') ?>"id'] ?>/delete" 
       class="btn btn-danger btn-sm" 
       onclick="return confirm('Are you sure?')">Delete</a>
</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const setId = this.dataset.setId;
        const status = this.value;
        
        fetch(`/admin/quiz/sets/${setId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                toastr.success('Status updated successfully');
            } else {
                toastr.error('Error updating status');
                // Revert selection if failed
                this.value = this.value === 'draft' ? 'published' : 'draft';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('Error updating status');
            // Revert selection if failed
            this.value = this.value === 'draft' ? 'published' : 'draft';
        });
    });
});
</script>