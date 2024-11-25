<h1>Mock Test Attempts History</h1>
<div class="row">
    <div class="breadcrumb">
        <a href="/admin/mocktest/attempts">Mock Tests</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Attempts History</a>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>S.N</th>
            <th>Student Name</th>
            <th>Test Name</th>
            <th>Score (%)</th>
            <th>Correct</th>
            <th>Wrong</th>
            <th>Unattempted</th>
            <th>Time Taken</th>
            <th>Completed At</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; ?>
        <?php foreach ($attempts as $attempt): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($attempt['username']) ?></td>
                <td><?= htmlspecialchars($attempt['mock_test_name']) ?></td>
                <td><?= number_format($attempt['score'], 1) ?>%</td>
                <td><?= $attempt['correct_answers'] ?></td>
                <td><?= $attempt['wrong_answers'] ?></td>
                <td><?= $attempt['unattempted'] ?></td>
                <td><?= gmdate('H:i:s', $attempt['time_taken']) ?></td>
                <td><?= date('M d, Y H:i', strtotime($attempt['completed_at'])) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>