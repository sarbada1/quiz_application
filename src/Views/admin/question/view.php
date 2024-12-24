<h1>List Question</h1>
<div class="row">
    <div class="breadcrumb">
        <a href="/quiz-play/admin/question/list">Question</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">List</a>
    </div>
    <div>
        <button class='success mb-5'><a href='/quiz-play/admin/question/add'>Add Question</a></button>
    </div>
</div>

<div class="mb-4">
    <h4>Filter Questions</h4>
    <form id="filterForm" method="get" action="">
        <div class="form-group">
            <label for="quiz_id">Quiz:</label>
            <select name="quiz" id="quiz_id" class="form-control">
                <option value="">--Select Quiz--</option>
                <?php foreach ($quizzes as $quiz): ?>
                    <option value="<?= $quiz['id'] ?>" <?= $selectedQuiz == $quiz['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($quiz['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="question_type">Question Type:</label>
            <select name="question_type" id="question_type" class="form-control">
                <option value="">--Select Question Type--</option>
                <?php foreach ($questionTypes as $type): ?>
                    <option value="<?= $type['id'] ?>" <?= $questionType == $type['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($type['type']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mb-5">Filter</button>
    </form>
</div>
<?php if (empty($questions)): ?>
    <table>
        <tr>
            <td>   No questions available.
            </td>
        </tr>
    </table>
<?php else: ?>
<?php
$currentQuiz = null;
$i = 1;
foreach ($questions as $question):
    if ($currentQuiz !== $question['title']):
        if ($currentQuiz !== null): ?>
            </tbody>
        </table>
        <?php endif; 
        $currentQuiz = $question['title'];
        ?>
        <h3 class="mt-4 mb-3"><?= htmlspecialchars($question['title']) ?></h3>
        <table class="questions-table">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Question</th>
                    <th>Type</th>
                    <th>Answer</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    <?php endif; ?>
    <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($question['question_text']) ?></td>
        <td><?= htmlspecialchars($question['type']) ?></td>
        <td>
            <button class="success mb-5"><a href="/quiz-play/admin/answer/add/<?= $question['id'] ?>">Add</a></button>
            <button class="warning"><a href="/quiz-play/admin/answer/list/<?= $question['id'] ?>">View</a></button>
        </td>
        <td>
            <button class="primary mb-5"><a href="/quiz-play/admin/question/edit/<?= $question['id'] ?>">Edit</a></button>
            <button class="danger"><a href="/quiz-play/admin/question/delete/<?= $question['id'] ?>" onclick="return confirm('Are you sure to delete?')">Delete</a></button>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>

<!-- Pagination -->
<div class="pagination mt-4">
    <?php if ($totalPages > 1): ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?><?= $selectedQuiz ? '&quiz='.$selectedQuiz : '' ?><?= $questionType ? '&question_type='.$questionType : '' ?>" 
               class="page-link <?= $currentPage == $i ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    <?php endif; ?>
</div>

<style>
.pagination {
    display: flex;
    gap: 5px;
    justify-content: center;
}
.page-link {
    padding: 5px 10px;
    border: 1px solid #ddd;
    text-decoration: none;
}
.page-link.active {
    background-color: #007bff;
    color: white;
}
.questions-table {
    width: 100%;
    margin-bottom: 20px;
}
</style>