<!-- views/admin/question/word.php -->
<div class="container">
    <h2>Import Questions from Text</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['status'] ?>" role="alert">
            <?= $_SESSION['message'] ?>
        </div>
    <?php endif; ?>

    <div class="mb-4">
        <h4>Instructions:</h4>
        <ol>
            <li>Copy all content from your Word document</li>
            <li>Paste it into the text area below</li>
            <li>Make sure questions are numbered and have answer options a-d</li>
            <li>Make sure "Answers" section is included at the end</li>
            <li>Select the quiz to add these questions to</li>
        </ol>
    </div>

    <form method="post" action="/admin/question/import-text">
        <div class="form-group mb-3">
            <label for="quiz_id">Select Quiz:</label>
            <select name="quiz_id" required class="form-control">
                <option value="">--Select quiz--</option>
                <?php foreach ($quizzes as $quiz): ?>
                    <option value="<?= $quiz['id'] ?>"><?= htmlspecialchars($quiz['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="question_content">Paste Questions and Answers:</label>
            <textarea name="question_content" required class="form-control" rows="20" 
                      placeholder="Paste your questions and answers here..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Import Questions</button>
    </form>
</div>