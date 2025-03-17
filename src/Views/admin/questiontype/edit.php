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
    <h2>Edit Question Type</h2>
    <div class="row">
        <div class="breadcrumb">
            <a href="<?= $url('admin/questiontype/list') ?>">Question Type</a>
            <i class="fas fa-chevron-right"></i>
            <a href="#" style="margin-left: 7px;cursor:default">Edit</a>
        </div>
        <div>
            <button class='danger mb-5'><a href="<?= $url('admin/questiontype/delete/<?= $category[') ?>"id'] ?>' onclick="return confirm('Are you sure to delete?')">Delete</a></button>
        </div>
    </div>
    <label for="name">Type:</label>
    <input type="text" id="name" name="type" value="<?= htmlspecialchars($category['type']) ?>" >

    <label for="slug">Slug:</label>
    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($category['slug']) ?>" >
    <label for="time_per_question">Time per question(in second):</label>
    <input type="text" id="time_per_question" name="time_per_question" value="<?= htmlspecialchars($category['time_per_question']) ?>" >

    <button class="success mt-5" type="submit">Save changes</button>
</form>