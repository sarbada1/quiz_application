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
<form action="<?= $url('admin/question/edit/' . $question['id']) ?>" method="POST" class="form-group">
    <h2>Edit Question</h2>
    <div class="breadcrumb">
        <a href="<?= $url('admin/question/list') ?>">Question</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Edit</a>
    </div>

    <label for="question_text">Question:</label>
    <input type="text" id="question_text" name="question_text" value="<?= htmlspecialchars($question['question_text']) ?>" required>

    <div class="form-group">
        <label>Question Type</label>
        <select name="question_type" required class="form-control">
            <option disabled>--Select question type--</option>
            <option value="mock" <?= $question['question_type'] == 'mock' ? 'selected' : '' ?>>Mock Test</option>
            <option value="previous_year" <?= $question['question_type'] == 'previous_year' ? 'selected' : '' ?>>Previous Year</option>
            <option value="quiz" <?= $question['question_type'] == 'quiz' ? 'selected' : '' ?>>Quiz</option>
            <option value="real_exam" <?= $question['question_type'] == 'real_exam' ? 'selected' : '' ?>>Real Exam</option>
        </select>
    </div>

    <div class="form-group">
        <label for="tags">Tags:</label>
        <select name="tags[]" id="tags" multiple class="form-control select2">
            <?php foreach ($tags as $tag): ?>
                <option value="<?= $tag['id'] ?>" <?= in_array($tag['id'], $questionTags) ? 'selected' : '' ?>><?= htmlspecialchars($tag['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">Select tags to filter available categories</small>
    </div>

    <div class="form-group">
        <label for="category_id">Category:</label>
        <select id="category_id" name="category_id" required class="form-control select2">
            <option value="">--Select Category--</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= $category['id'] == $question['category_id'] ? 'selected' : '' ?> data-parent="<?= $category['parent_id'] ?? 0 ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="difficulty_level">Level of difficulty:</label>
        <select id="difficulty_level" name="difficulty_level" class="form-control">
            <option value="0">--Select Level--</option>
            <?php foreach ($levels as $level): ?>
                <option value="<?= $level['id'] ?>" <?= $level['id'] == $question['difficulty_level'] ? 'selected' : '' ?>><?= htmlspecialchars($level['level']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="marks">Marks:</label>
        <input type="number" id="marks" name="marks" min="1" value="<?= htmlspecialchars($question['marks']) ?>" required class="form-control">
    </div>

    <div class="form-group">
        <label for="year">Year:</label>
        <input type="text" id="year" name="year" value="<?= htmlspecialchars($question['year']) ?>" class="form-control">
    </div>

    <button class="success mt-5" type="submit">Save changes</button>
</form>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for tags and categories
    $('.select2').select2({
        width: '100%'
    });
    
    // Store all categories with their data
    const allCategories = <?= json_encode($categories) ?>;
    const currentCategoryId = <?= json_encode($question['category_id']) ?>;
    
    // When tags change, update available categories
    $('#tags').on('change', function() {
        const selectedTagIds = $(this).val();
        
        if (!selectedTagIds || selectedTagIds.length === 0) {
            // If no tags selected, show all categories
            resetCategoryDropdown();
            return;
        }
        
        // Fetch categories for selected tags
        $.ajax({
            url: '<?= $url('admin/category/get-by-tags') ?>',
            type: 'POST',
            data: { tag_ids: selectedTagIds },
            dataType: 'json',
            success: function(response) {
                updateCategoryDropdown(response.categories);
            },
            error: function() {
                alert('Failed to fetch categories for selected tags');
                resetCategoryDropdown();
            }
        });
    });
    
    function updateCategoryDropdown(categories) {
        const categorySelect = $('#category_id');
        
        // Clear current options except the default one
        categorySelect.find('option:not(:first)').remove();
        
        // Sort categories by parent relationship
        const parentCategories = categories.filter(c => !c.parent_id || c.parent_id === 0);
        const childCategories = categories.filter(c => c.parent_id > 0);
        
        // Add parent categories first
        parentCategories.forEach(category => {
            categorySelect.append(new Option(
                category.name, 
                category.id, 
                false, 
                category.id == currentCategoryId
            ));
        });
        
        // Add child categories with indentation
        childCategories.forEach(category => {
            const parentName = findCategoryName(category.parent_id);
            const optionText = `${parentName} → ${category.name}`;
            
            categorySelect.append(new Option(
                optionText, 
                category.id, 
                false, 
                category.id == currentCategoryId
            ));
        });
        
        // Refresh Select2
        categorySelect.trigger('change');
    }
    
    function resetCategoryDropdown() {
        const categorySelect = $('#category_id');
        
        // Clear current options except the default one
        categorySelect.find('option:not(:first)').remove();
        
        // Add all categories back
        allCategories.forEach(category => {
            categorySelect.append(new Option(
                category.name, 
                category.id, 
                false, 
                category.id == currentCategoryId
            ));
        });
        
        // Refresh Select2
        categorySelect.trigger('change');
    }
    
    function findCategoryName(categoryId) {
        const category = allCategories.find(c => c.id == categoryId);
        return category ? category.name : '';
    }
});
</script>