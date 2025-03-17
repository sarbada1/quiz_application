<div class="container">
    <div class="flex w-100 justify-between">

        <h1>Online Mock Test</h1>
        <!-- <button class='warning mb-5'><a href='/admin/mocktestquestion/add/<?= $mockTest['id'] ?>'>Add</a></button> -->
        <button class='warning mb-5'><a href='/admin/mocktestquestion/add/<?= $mockTest['id'] ?>'>Edit</a></button>
    </div>
    <div class="test-info">
        <div>
            <strong>Program:</strong> <span id="program-name"><?= htmlspecialchars($program['name']) ?></span>
        </div>
        <div>
            <strong>Test:</strong> <span id="test-name"><?= htmlspecialchars($mockTest['name']) ?></span>
        </div>
       
        <?php
        $timeInSeconds = intval($mockTest['time']);
        $hours = floor($timeInSeconds / 3600);
        $minutes = floor(($timeInSeconds % 3600) / 60);
        ?>
    
        <div class="timer" id="timer">
            Time:
            <?php if ($hours > 0): ?>
                <?= $hours ?> hours and <?= $minutes ?> minutes
            <?php else: ?>
                <?= $minutes ?> minutes
            <?php endif; ?>
        </div>

    </div>
    <?php
    if ($questions) {
    ?>
        <div id="questions">
            <?php foreach ($questions as $index => $question): ?>
                <div class="question">
                    <h3>Question <?= $index + 1 ?>: <?= htmlspecialchars($question['question_text']) ?></h3>
                    <div class="answers">
                        <?php foreach ($question['answers'] as $answer): ?>
                            <div class="answer <?= $answer['isCorrect'] ? 'correct' : '' ?>">
                                <?= htmlspecialchars($answer['answer']) ?>
                                <?= $answer['isCorrect'] ? ' (Correct)' : '' ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($question['reason'])): ?>
                        <div class="reason">
                            <strong>Reason:</strong> <?= htmlspecialchars($question['reason']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php
    }else{
        ?>
<p >No questions available</p>
        <?php
    }
    ?>
</div>