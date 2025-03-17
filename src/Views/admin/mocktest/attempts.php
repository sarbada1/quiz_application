<!-- attempts.php -->
<h1>Mock Test Attempts History</h1>
<div class="row">
    <div class="breadcrumb">
        <a href="<?= $url('admin/mocktest/attempts') ?>">Mock Tests</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">Attempts History</a>
    </div>
</div>

<?php
// Group attempts by mock test name
$groupedAttempts = [];
foreach ($attempts as $attempt) {
    $testName = $attempt['mock_test_name'];
    if (!isset($groupedAttempts[$testName])) {
        $groupedAttempts[$testName] = [];
    }
    $groupedAttempts[$testName][] = $attempt;
}
?>

<div class="attempts-container">
    <?php foreach ($groupedAttempts as $testName => $testAttempts): ?>
        <div class="test-group">
            <div class="test-header" onclick="toggleAttempts('<?= md5($testName) ?>')">
                <h3><?= htmlspecialchars($testName) ?></h3>
                <span class="attempt-count"><?= count($testAttempts) ?> attempts</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div id="attempts-<?= md5($testName) ?>" class="attempts-table" style="display: none;">
                <table>
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Student Name</th>
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
                        <?php foreach ($testAttempts as $attempt): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($attempt['username']) ?></td>
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
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
.attempts-container {
    max-width: 1200px;
    margin: 20px auto;
}

.test-group {
    margin-bottom: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}

.test-header {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    background: #f8f9fa;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.test-header:hover {
    background: #e9ecef;
}

.test-header h3 {
    flex: 1;
    margin: 0;
    color: #2c3e50;
}

.attempt-count {
    margin-right: 15px;
    color: #6c757d;
    font-size: 0.9em;
}

.fa-chevron-down {
    transition: transform 0.3s ease;
}

.test-header.active .fa-chevron-down {
    transform: rotate(180deg);
}

.attempts-table {
    padding: 20px;
    background: white;
}

.attempts-table table {
    width: 100%;
    border-collapse: collapse;
}

.attempts-table th,
.attempts-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.attempts-table th {
    background: #f8f9fa;
    font-weight: 600;
}

.attempts-table tr:hover {
    background: #f8f9fa;
}

@media (max-width: 768px) {
    .attempts-table {
        overflow-x: auto;
    }
    
    .test-header {
        flex-wrap: wrap;
    }
}
</style>

<script>
function toggleAttempts(testId) {
    const tableDiv = document.getElementById(`attempts-${testId}`);
    const header = tableDiv.previousElementSibling;
    const isHidden = tableDiv.style.display === 'none';
    
    tableDiv.style.display = isHidden ? 'block' : 'none';
    header.classList.toggle('active');
}
</script>