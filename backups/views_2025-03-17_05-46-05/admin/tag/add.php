<?php if (isset($_SESSION['message'])): ?>
    <div id="alert" class="alert alert-<?= $_SESSION['status'] ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= $_SESSION['message'] ?>
    </div>
    <?php unset($_SESSION['message']); unset($_SESSION['status']); ?>
<?php endif; ?>
<form action="/admin/tag/add" method="POST" class="form-group">
    <h2>Add New Tag</h2>
    <div class="breadcrumb">
        <a href="/admin/tag/list">Tags</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Create</a>
    </div>
    
    <label for="name">Name:</label>
    <input type="text" id="title" name="name" required>

    <label for="slug">Slug:</label>
    <input type="text" id="slug" name="slug" readonly>
    
    <button class="success mt-5" type="submit">Create</button>
</form>

<script>
document.getElementById('title').addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-');
    document.getElementById('slug').value = slug;
});
</script>