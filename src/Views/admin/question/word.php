<!-- views/admin/question/word.php -->
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
            <div class="form-group">
                <label for="tags">Tags:</label>
                <select name="tags[]" id="tags" multiple class="form-control">
                    <?php foreach ($tags as $tag): ?>
                        <option value="<?= $tag['id'] ?>"><?= htmlspecialchars($tag['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="tags">Categories:</label>
                <select name="category_id" id="category_id" class="form-control">
                    <option value="">--Select Category--</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Question Type</label>
                <select name="question_type" required>
                    <option selected disabled>--Select question type--</option>
                    <option value="mock">Mock Test</option>
                    <option value="previous_year">Previous Year</option>
                    <option value="quiz"> Quiz</option>
                    <option value="real_exam">Real Exam</option>
                </select>
            </div>
            <div class="form-group">
                <label for="difficulty_level"> Level of difficulty:</label>
                <select id="difficulty_level" name="difficulty_level">
                    <option value="0">--Select Level--</option>
                    <?php foreach ($levels as $level): ?>
                        <option value="<?= $level['id'] ?>"><?= htmlspecialchars($level['level']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="year">Year:</label>
            <input type="text" id="year" name="year">
        </div>

        <div class="form-group mb-3">
            <label for="question_content">Paste Questions and Answers:</label>
            <textarea name="question_content" required class="form-control" rows="20"
                placeholder="Paste your questions and answers here..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Import Questions</button>
    </form>
</div>

<script>
    // Add select2 for better tag selection
    $(document).ready(function() {
        $('select[name="tags[]"]').select2({
            placeholder: 'Select tags',
            allowClear: true
        });
    });
</script>