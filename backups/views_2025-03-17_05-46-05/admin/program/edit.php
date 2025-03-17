<!-- edit.php -->
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
<form action="/admin/program/edit/<?= $program['id'] ?>" method="POST" class="form-group">
    <h2>Edit Test</h2>
    <div class="breadcrumb">
        <a href="/admin/program/list">Test</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Edit</a>
    </div>
    
    <div class="form-group">
        <label for="category_id">Category:</label>
        <select name="category_id" id="category_id" required>
            <option value="">Select a category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= ($category['id'] == $program['category_id']) ? 'selected' : '' ?>>
                    <?= $category['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="title">Test Name:</label>
        <input type="text" id="title" name="name" value="<?= htmlspecialchars($program['name']) ?>" required>
    </div>

    <label for="slug">Slug:</label>
    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($program['slug']) ?>"  readonly>

    <div class="form-group">
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($program['description']) ?></textarea>
    </div>

    <button class="success mt-5" type="submit">Update Program</button>
</form>