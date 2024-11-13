<h1>List Question</h1>
<div class="row">
    <div class="breadcrumb">
        <a href="/admin/question/list">Question</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">List</a>
    </div>
    <div>
        <button class='success mb-5'><a href='/admin/question/add'>Add Question</a></button>
    </div>
</div>

<form action="/admin/question/list" method="GET" id="quiz-filter-form">
    <div class="filter-container">
        <label for="quiz-filter">Filter by Quiz:</label>
        <select id="quiz-filter" name="quiz" onchange="filterQuestion(this.value)">
            <option value="0">All Quizzes</option>
            <?php foreach ($quizzes as $quiz): ?>
                <option value="<?= urlencode($quiz['id']) ?>" <?= $selectedQuiz === $quiz['title'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($quiz['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</form>

<table id="questions-table" class="mt-5">
    <thead>
        <tr>
            <th>SN</th>
            <th>Question</th>
            <th>Quiz</th>
            <th>Type</th>
            <th>Answer</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="data">
        <?php
        $i = 1;
        foreach ($questions as $question): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?= htmlspecialchars($question['question_text']) ?></td>
                <td><?= htmlspecialchars($question['title']) ?></td>
                <td><?= htmlspecialchars($question['type']) ?></td>
                <td>
                    <button class="success"> <a href="/admin/answer/add/<?= $question['id'] ?>">Add</a></button>
                    <button class="warning"> <a href="/admin/answer/list/<?= $question['id'] ?>">View</a></button>
                </td>
                <td>
                    <button class="primary"> <a href="/admin/question/edit/<?= $question['id'] ?>">Edit</a></button>
                    <button class="danger"> <a href="/admin/question/delete/<?= $question['id'] ?>" onclick="return confirm('Are you sure to delete?')">Delete</a></button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
function filterQuestion(str) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (this.status == 200) {
                try {
                    // console.log(this.responseText);
                    
                    document.getElementById("data").innerHTML = this.responseText;
                } catch (e) {
                    console.error("Error parsing JSON response:", e);
                }
            } else {
                console.error("Request failed with status:", this.status);
            }
        }
    };
    xmlhttp.open("GET", "/ajax/filter-questions/" + str, true);
    xmlhttp.send();
}
</script>