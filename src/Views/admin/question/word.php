<style>
    .example-format {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border-left: 4px solid #17a2b8;
    }

    .example-format pre {
        margin: 0;
        white-space: pre-wrap;
        font-family: monospace;
    }

    .alert-info h4 {
        color: #0c5460;
        margin-bottom: 1rem;
    }

    .alert-info ol {
        margin-bottom: 0;
    }

    .alert-info li {
        margin-bottom: 0.5rem;
    }
    
    .tag-category-section {
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #e9ecef;
        border-radius: 5px;
        background-color: #f8f9fa;
    }
    
    .category-selection-container {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px dashed #dee2e6;
    }
    
    #categorySelection {
        min-height: 100px;
    }
    
    .select2-container {
        width: 100% !important;
    }
</style>

<div class="container">
    <h2>Import Questions from Text</h2>

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

    <div class="alert alert-info">
        <h4>Instructions:</h4>
        <ol>
            <li>Questions must be numbered (1., 2., etc.)</li>
            <li>Options must start with a., b., c., d. on new lines</li>
            <li>Leave a blank line between questions</li>
            <li>Add answers section at the end with format: question_number. answer_letter</li>
        </ol>

        <div class="example-format mt-3">
            <h5>Example Format:</h5>
            <pre>
1. What is the capital of Nepal?
   a. Kathmandu
   b. Pokhara
   c. Biratnagar
   d. Chitwan

2. Which is the highest mountain in the world?
   a. K2
   b. Mount Everest
   c. Kanchenjunga
   d. Makalu

Answers:
1. a
2. b</pre>
        </div>
    </div>

    <form method="post" action="<?= $url('admin/question/import-text') ?>">
        <input type="hidden" name="quiz_id" value=<?= $id ?? '' ?>>
        <?php if (!($id)): ?>
            <div class="tag-category-section">
                <h4>Question Classification</h4>
                <p class="text-muted">Select course and subject for these questions</p>

                <div class="form-group">
        <label for="tags">Tags:</label>
        <select name="tag_id" id="tags" class="form-control ">
            <option selected disabled>--Select tag--</option>
            <?php foreach ($tags as $tag): ?>
                <option value="<?= $tag['id'] ?>"><?= htmlspecialchars($tag['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">Select tags to filter available categories</small>
    </div>

    <div class="form-group">
        <label for="category_id">Category:</label>
        <select id="category_id" name="category_id" required class="form-control ">
            <option value="">--Select Category--</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" data-parent="<?= $category['parent_id'] ?? 0 ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
            </div>
     
            <!-- <div class="form-group">
                <label>Question Type</label>
                <select name="question_type" class="form-control" required>
                    <option value="" disabled selected>--Select question type--</option>
                    <option value="mock">Mock Test</option>
                    <option value="previous_year">Previous Year</option>
                    <option value="quiz">Quiz</option>
                    <option value="real_exam">Real Exam</option>
                </select>
            </div> -->
            <div class="form-group">
                <label for="difficulty_level">Level of difficulty:</label>
                <select id="difficulty_level" name="difficulty_level" class="form-control">
                    <option value="0">--Select Level--</option>
                    <?php foreach ($levels as $level): ?>
                        <option value="<?= $level['id'] ?>"><?= htmlspecialchars($level['level']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="year">Year:</label>
            <input type="text" id="year" name="year" class="form-control">
        </div>

        <div class="form-group mb-3">
            <label for="question_content">Paste Questions and Answers:</label>
            <textarea name="question_content" required class="form-control" rows="20"
                placeholder="Paste your questions and answers here..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Import Questions</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
    $(document).ready(function() {


        // Load categories when tag changes
        $('#tag_id').on('change', function() {
            const selectedTagId = $(this).val();
            
            if (selectedTagId) {
                loadCategoriesForTag(selectedTagId);
            } else {
                // Clear categories if no tag selected
                $('#category_id').empty().append('<option value="" disabled selected>Select a course first</option>');
                $('#category_id').trigger('change');
            }
        });
    });
    
    // Function to load categories associated with selected tag
    function loadCategoriesForTag(tagId) {
        $('#categoryLoading').removeClass('d-none');
        
        $.ajax({
            url: '<?= $url('admin/get-categories-by-tags') ?>',
            type: 'POST',
            data: { tagIds: [tagId] },
            success: function(response) {
                // Parse response if needed
                let categories;
                try {
                    if (typeof response === 'string') {
                        categories = JSON.parse(response);
                    } else {
                        categories = response;
                    }
                    
                    // Update category dropdown
                    $('#category_id').empty();
                    
                    if (categories.length === 0) {
                        $('#category_id').append('<option value="" disabled selected>No categories found for selected course</option>');
                    } else {
                        $('#category_id').append('<option value="" disabled selected>Select a subject category</option>');
                        
                        // Group categories by parent
                        const categoriesByParent = {};
                        const topLevelCategories = [];
                        
                        categories.forEach(function(category) {
                            if (category.parent_id) {
                                if (!categoriesByParent[category.parent_id]) {
                                    categoriesByParent[category.parent_id] = [];
                                }
                                categoriesByParent[category.parent_id].push(category);
                            } else {
                                topLevelCategories.push(category);
                            }
                        });
                        
                        // Add top-level categories
                        topLevelCategories.forEach(function(category) {
                            $('#category_id').append(`<option value="${category.id}">${category.name}</option>`);
                            
                            // Add child categories if exists
                            if (categoriesByParent[category.id]) {
                                categoriesByParent[category.id].forEach(function(child) {
                                    $('#category_id').append(`<option value="${child.id}">&nbsp;&nbsp;&nbsp;└ ${child.name}</option>`);
                                });
                            }
                        });
                    }
                    
                    $('#category_id').trigger('change');
                } catch (e) {
                    console.error('Error parsing category data:', e);
                    $('#category_id').empty().append('<option value="" disabled selected>Error loading categories</option>');
                }
                
                $('#categoryLoading').addClass('d-none');
            },
            error: function() {
                $('#category_id').empty().append('<option value="" disabled selected>Error loading categories</option>');
                $('#categoryLoading').addClass('d-none');
            }
        });
    }
</script>
