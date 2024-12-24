<!-- views/admin/mocktestquestion/add.php -->
<div class="container">
    <h2>Add Questions to Mock Test: <?= htmlspecialchars($mockTest['name']) ?></h2>
    <div class="row">
        <div class="breadcrumb">
            <a href="/admin/mocktest/list/<?= $mockTest['program_id'] ?>">MockTest</a>
            <i class="fas fa-chevron-right"></i>
            <a href="/admin/mocktestquestion/list/<?= $mockTest['id'] ?>" style="margin-left: 7px;cursor:default">Questions</a>
            <i class="fas fa-chevron-right"></i>
            <a href="#" style="margin-left: 7px;cursor:default">Add</a>
        </div>
    </div>
    <div class="mb-4">
        <h4>Instructions:</h4>
        <ol>
            <li>Use the filter options below to narrow down the list of questions by quiz and question type.</li>
            <li>Select the desired quiz and question type from the dropdown menus.</li>
            <li>Click the "Filter" button to apply the filters and display the relevant questions.</li>
            <li>To add questions to the mock test, check the boxes next to the questions you want to include.</li>
            <li>Once you have selected the questions, they will be automatically added to the mock test.</li>
        </ol>
    </div>
    <div class="mb-4">
        <h4>Filter Questions</h4>
        <form id="filterForm" method="get" action="">
            <div class="form-group">
                <label for="quiz_id">Quiz:</label>
                <select name="quiz_id" id="quiz_id" class="form-control">
                    <option value="">--Select Quiz--</option>
                    <?php foreach ($quizzes as $quiz): ?>
                        <option value="<?= $quiz['id'] ?>"><?= htmlspecialchars($quiz['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="question_type">Question Type:</label>
                <select name="question_type" id="question_type" class="form-control">
                    <option value="">--Select Question Type--</option>
                    <?php foreach ($questionTypes as $type): ?>
                        <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['type']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mb-5">Filter</button>
        </form>
    </div>

    <form method="post" action="/admin/mocktestquestion/add/<?= $mockTest['id'] ?>">
        <table class="table">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Type</th>
                    <th>Quiz</th>
                    <th>Add</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questions as $question): ?>
                    <tr>
                        <td><?= htmlspecialchars($question['question_text']) ?></td>
                        <td><?= htmlspecialchars($question['type']) ?></td>
                        <td><?= htmlspecialchars($question['quiz_title']) ?></td>
                        <td>
                            <input type="checkbox"
                                id="question_<?= $question['id'] ?>"
                                <?= in_array($question['id'], $existingQuestions) ? 'checked' : '' ?>
                                onclick="toggleQuestion(<?= $question['id'] ?>, <?= $mockTest['id'] ?>)">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</div>

<script>
    function toggleQuestion(questionId, mockTestId) {
        var isChecked = document.getElementById('question_' + questionId).checked;
        var action = isChecked ? 'add' : 'remove';
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        };
        xmlhttp.open("GET", "/ajax/toggle-question/" + action + "/" + questionId + "/" + mockTestId, true);
        xmlhttp.send();
    }
</script>