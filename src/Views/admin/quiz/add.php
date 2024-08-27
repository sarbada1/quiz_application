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
<form action="/admin/quiz/add" method="POST" class="form-group">
<h2>Add Quiz</h2>
<div class="breadcrumb">
        <a href="/admin/quiz/list">Quiz</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Create</a>
    </div>
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" >
    <label for="slug">Slug:</label>
    <input type="text" id="slug" name="slug" readonly>
    <label for="description">Description:</label>
    <textarea name="description" id="description"></textarea>
    <label for="category_id"> Category:</label>
    <select id="category_id" name="category_id">
        <option value="0" selected disabled>None (Top Level)</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <label for="difficulty_level"> Level of difficulty:</label>
    <select id="difficulty_level" name="difficulty_level">
        <option value="0">--Select Level--</option>
        <?php foreach ($levels as $level): ?>
            <option value="<?= $level['id'] ?>"><?= htmlspecialchars($level['level']) ?></option>
        <?php endforeach; ?>
    </select>
    
    <button class="success mt-5" type="submit">Create</button>
</form>