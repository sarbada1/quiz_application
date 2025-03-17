<div class="container">
    <h2><?= htmlspecialchars($quiz['title']) ?> - Questions</h2>
    <div class="quiz-info">
        <p><?= htmlspecialchars($quiz['description']) ?></p>
        <p>Total Marks: <?= $quiz['total_marks'] ?></p>
        <p>Number of Questions: <?= count($questions) ?></p>
    </div>

    <?php if (!empty($questions)): ?>
        <table class="table mt-5">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>Question</th>
                    <th>Answers</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($questions as $questionId => $question): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($question['question_text']) ?></td>
                        <td>
                            <ul>
                                <?php foreach ($question['answers'] as $answer): ?>
                                    <li><?= htmlspecialchars($answer['answer_text']) ?> (<?= $answer['is_correct'] ? 'Correct' : 'Incorrect' ?>)</li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No questions available for this quiz.</p>
    <?php endif; ?>
</div>