<h1>Add Question for Mock Test: <?= $mockTest['name'] ?></h1>
<div class="row">
    <div class="breadcrumb">
        <a href="/admin/mocktest/list/<?= $mockTest['program_id'] ?>">MockTest</a>
        <i class="fas fa-chevron-right"></i>
        <a href="/admin/mocktestquestion/list/<?= $mockTest['id'] ?>" style="margin-left: 7px;cursor:default">Questions</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Add</a>
    </div>
</div>
<form method="post" action="" class="form-group">
    <table>
        <tbody>
            <?php
            $i = 1;
            // $addedQuestions is an array of questions already added to the mock test
            foreach ($questions as $question) {
                // Check if the question is already associated with the mock test
                $isChecked = in_array($question['id'], $existingQuestions) ? 'checked' : '';
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><label for="question_<?= $question['id'] ?>"><?= $question['question_text'] ?></label></td>
                    <td>
                        <input 
                            type="checkbox" 
                            onclick="toggleQuestion(<?= $question['id'] ?>,<?= $mockTest['id'] ?>)"  
                            value="<?= $question['id'] ?>" 
                            id="question_<?= $question['id'] ?>" 
                            <?= $isChecked ?>
                        >
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</form>

<script>
function toggleQuestion(questionId, mockTestId) {
    var isChecked = document.getElementById('question_' + questionId).checked;

    var action = isChecked ? 'add' : 'remove'; // Decide if we are adding or removing
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText); // Handle response if needed
        }
    };
    xmlhttp.open("GET", "/ajax/toggle-question/" + action + "/" + questionId + "/" + mockTestId, true);
    xmlhttp.send();
}
</script>
