<?php if (isset($_SESSION['message']) && $_SESSION['message']): ?>
<div id="success-alert" class="alert alert-<?=$_SESSION['status']?>" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button> 
    <?= $_SESSION['message'] ?>   
</div>
<?php 
 unset($_SESSION['message']);
 unset($_SESSION['status']);
endif; ?>
<form action="/admin/questiontype/add" method="POST" class="form-group">
<h2>Add Question Type</h2>
<div class="breadcrumb">
        <a href="/admin/questiontype/list">Question Type</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Create</a>
    </div>
    <label for="name">Type:</label>
    <input type="text" id="name" name="type" required>
    <label for="slug">Slug:</label>
    <input type="text" id="slug" name="slug" required>
    <label for="time_per_question">Time per question(in second):</label>
    <input type="text" id="time_per_question" name="time_per_question" required>
    
    <button class="success mt-5" type="submit">Create</button>
</form>