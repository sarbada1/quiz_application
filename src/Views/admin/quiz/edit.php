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
<form method="POST" class="form-group">
    <h1>Edit Quiz</h1>
    <div class="row">
        <div class="breadcrumb">
            <a href="/admin/quiz/list">Quiz</a>
            <i class="fas fa-chevron-right"></i>
            <a href="#" style="margin-left: 7px;cursor:default">Edit</a>
        </div>
        <div>
            <button class='danger mb-5'><a href='/admin/quiz/delete/<?= $category['id'] ?>' onclick="return confirm('Are you sure to delete?')">Delete</a></button>
        </div>
    </div>
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="<?= htmlspecialchars($category['title']) ?>">

    <label for="slug">Slug:</label>
    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($category['slug']) ?>" readonly>

    <label for="description">Description:</label>
    <textarea name="description" id="description"><?= htmlspecialchars($category['description']) ?></textarea>

    <label for="category_id"> Category:</label>
    <select id="category_id" name="category_id">
        <option value="0" <?= $category['category_id'] == 0 ? 'selected' : '' ?>>None (Top Level)</option>
        <?php foreach ($categories as $cat): ?>
            <?php if ($cat['id'] != $category['id']): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $category['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>

    <label for="difficulty_level"> Level of difficulty:</label>
    <select id="difficulty_level" name="difficulty_level">
        <option value="0" >--Select Level--</option>
        <?php foreach ($levels as $level): ?>
            <?php if ($cat['id'] != $category['id']): ?>
                <option value="<?= $level['id'] ?>" <?= $level['id'] == $category['difficulty_level'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($level['level']) ?>
                </option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>


    <button type="submit" class="warning mt-5">Save changes</button>
</form>