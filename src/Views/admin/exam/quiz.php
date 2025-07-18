<?php if (isset($_SESSION['message'])): ?>
    <div id="alert" class="alert alert-<?= $_SESSION['status'] ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= $_SESSION['message'] ?>
    </div>
<?php
    unset($_SESSION['message']);
    unset($_SESSION['status']);
endif;
?>
<?php if (isset($program)): ?>
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Quiz</h2>
            <div class="header-actions">
                <a href="<?= $url('admin/quiz/add') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Quiz
                </a>
            </div>
        </div>

        <table class="table mt-5">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>Title</th>
                    <th>Time (minutes)</th>
                    <th>Total Marks</th>
                    <th>Status</th>
                
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($quizzes)): ?>
                    <?php $i = 1;
                    foreach ($quizzes as $quiz): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($quiz['title']) ?></td>
                            <td><?= $quiz['duration'] ?></td>
                            <td><?= $quiz['total_marks'] ?></td>

                            <td>
                                <span class="badge badge-<?= $quiz['status'] ?>">
                                    <?= ucfirst($quiz['status']) ?>
                                </span>
                            </td>
                       
                       
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>