<div class="content-wrapper">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['status'] ?>" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
            <?= $_SESSION['message'] ?>
        </div>
    <?php 
        unset($_SESSION['message']);
        unset($_SESSION['status']);
    endif; ?>

    <div class="content-header">
        <h1>Quiz List</h1>
        <div class="header-actions">
            <a href="/admin/quiz/add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Quiz
            </a>
        </div>
    </div>

    <div class="quiz-list-container">
        <table class="table">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Categories</th>
                    <th>Tags</th>
                    <th>Marks</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($quizzes as $quiz): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($quiz['title']) ?></td>
                        <td>
                            <span class="badge badge-<?= $quiz['type'] ?>">
                                <?= ucfirst($quiz['type']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($quiz['categories'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($quiz['tags'] ?? 'N/A') ?></td>
                        <td><?= $quiz['total_marks'] ?></td>
                        <td><?= $quiz['duration'] ?> min</td>
                        <td>
                            <span class="badge badge-<?= $quiz['status'] ?>">
                                <?= ucfirst($quiz['status']) ?>
                            </span>
                        </td>
                        <td class="actions">

                            
                            <a href="/admin/quiz/edit/<?= $quiz['id'] ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <a href="/admin/quiz/delete/<?= $quiz['id'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this quiz?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.content-wrapper {
    padding: 20px;
}

.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.quiz-list-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
}

.table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

.badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
}

.badge-mock { background: #17a2b8; color: white; }
.badge-quiz { background: #28a745; color: white; }
.badge-previous_year { background: #ffc107; color: black; }
.badge-real_exam { background: #dc3545; color: white; }

.badge-draft { background: #6c757d; color: white; }
.badge-published { background: #28a745; color: white; }

.actions {
    display: flex;
    gap: 5px;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
}
</style>