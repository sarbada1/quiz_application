<h1>Answers for Question: <?= $question['question_text'] ?></h1>
<div class="row">
<div class="breadcrumb">
        <a href="<?= $url('admin/question/list') ?>">Question</a>
        <i class="fas fa-chevron-right"></i>
        <a href="<?= $url('admin/answer/list/<?= $question[') ?>"id'] ?>" style="margin-left: 7px;">Answers</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">List</a>
    </div>
    <div>
        <button class='success mb-5'><a href="<?= $url('admin/answer/add/<?= $question[') ?>"id'] ?>'>Add Answer</a></button>
    </div>
</div>
<table>
    <thead>
        <tr>
            <th>S.N</th>
            <th>Answer</th>
            <th>Reason</th>
            <th>Correct</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i=1; 
        foreach ($answers as $answer): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $answer['answer'] ?></td>
                <td><?= $answer['reason'] ?></td>
                <td><?= $answer['isCorrect'] ? 'Yes' : 'No' ?></td>
                <td>
                    <button class="primary"> <a href="<?= $url('admin/answer/edit/<?= $answer[') ?>"id'] ?>">Edit</a></button>
                    <button class="danger"> <a href="<?= $url('admin/answer/delete/<?= $answer[') ?>"id'] ?>" onclick="return confirm('Are you sure to delete?')">Delete</a></button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>