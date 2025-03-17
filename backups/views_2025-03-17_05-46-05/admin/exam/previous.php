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
            <h2>Previous Year Entrance </h2>
            <div class="header-actions">
                <a href="/admin/quiz/add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Previous Year Entrance
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
                    <th>Questions</th>
                    <th>Status</th>
                    <th>Year</th>
                    <!-- <th>Sets</th> -->
                    <!-- <th>Actions</th> -->
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($mocktests)): ?>
                    <?php $i = 1;
                    foreach ($mocktests as $quiz): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($quiz['title']) ?></td>
                            <td><?= $quiz['duration'] ?></td>
                            <td><?= $quiz['total_marks'] ?></td>
                            <td>
                                <a href="/admin/real_exam/question/<?= $quiz['id'] ?>"
                                    class="btn btn-info btn-sm">
                                    Add Questions
                                </a>
                                <a href="/admin/quiz/questions/<?= $quiz['id'] ?>" class="btn btn-info btn-sm">
                                    View Questions (<?= $quiz['question_count'] ?>)
                                </a>

                            </td>
                            <td>
                                <span class="badge badge-<?= $quiz['status'] ?>">
                                    <?= ucfirst($quiz['status']) ?>
                                </span>
                            </td>
                            <td>
                                <form action="/admin/quiz/updateYear/<?= $quiz['id'] ?>" method="POST">
                                    <input type="number" name="year" value="<?= htmlspecialchars($quiz['year'] ?? '') ?>" min="2000" max="<?= date('Y') ?>" required>
                                    <button type="submit" class="btn btn-primary btn-sm mt-5">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>