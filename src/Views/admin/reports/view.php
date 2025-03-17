<!-- admin/reports/view.php -->
<div class="reports-container">
    <h2>Question Reports</h2>
    <table class="reports-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Reporter</th>
                <th>Reason</th>
                <th>Description</th>
                <th>Status</th>
                <th>Date</th>
                <th>Source</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i=1;foreach ($reports as $report): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($report['question_text']) ?></td>
                    <td><?= htmlspecialchars($report['reporter_name']) ?></td>
                    <td><?= htmlspecialchars($report['reason']) ?></td>
                    <td><?= htmlspecialchars($report['description']) ?></td>
                    <td>
                    <select 
    class="status-select status-<?= $report['status'] ?>" 
    onchange="updateStatus(<?= $report['id'] ?>, this.value); this.className='status-select status-' + this.value;">
    <option value="pending" <?= $report['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
    <option value="reviewed" <?= $report['status'] === 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
    <option value="resolved" <?= $report['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
</select>
                    </td>
                    <td><?= date('Y-m-d H:i', strtotime($report['created_at'])) ?></td>
                    <td><?= $report['source'] === 'normal' ? 'Normal' : 'Previous Year' ?></td>
                    <td>
                        <button><a href="<?= $url('admin/answer/list/<?=$report[') ?>"question_id'] ?>">View Details</a></button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function updateStatus(reportId, status) {
    fetch(`/admin/reports/update/${reportId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            throw new Error(data.error || 'Update failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update status: ' + error.message);
    });
}
</script>