<style>
    /* Add to your existing CSS file */
    .subject-test-container {
        max-width: 1200px;
        margin: 100px auto;
        padding: 20px;
    }

    .subjects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .subject-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .chapters-list {
        margin-top: 15px;
        border-top: 1px solid #eee;
        padding-top: 15px;
    }

    .chapter-item {
        padding: 8px 0;
    }

    .chapter-item a {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #444;
        text-decoration: none;
    }

    .badge {
        background: #f0f0f0;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8em;
    }
</style>
<div class="subject-test-container">
    <h2><?= htmlspecialchars($program['name']) ?> - Subjects</h2>

    <?php if (!empty($subjects)) : ?>
        <div class="subjects-grid">
            <?php foreach ($subjects as $subject) : ?>
                <div class="subject-card">
                    <h3><?= htmlspecialchars($subject['name']) ?></h3>
                    <div class="meta-info">
                        <span class="question-count">
                            <i class="fas fa-question-circle"></i>
                            <?= $subject['question_count'] ?> Questions
                        </span>
                    </div>

                    <?php if (!empty($subject['chapters'])) : ?>
                        <div class="chapters-list">
                            <h4>Chapters</h4>
                            <?php foreach ($subject['chapters'] as $chapter) : ?>
                                <?php if ($chapter['question_count'] > 0) : ?>
                                    <div class="chapter-item">
                                        <a href="/test/chapter/<?= $chapter['id'] ?>">
                                            <?= htmlspecialchars($chapter['name']) ?>
                                            <span class="badge"><?= $chapter['question_count'] ?></span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($subject['question_count'] > 0) : ?>
                        <div class="button-group mt-5">
                            <a href="/test/subject/<?= $subject['id'] ?>" class="btn btn-primary mt-5">
                                Take Subject Test
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p class="no-subjects">No subjects with questions available.</p>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/auth/login.php'; ?>
<?php include __DIR__ . '/auth/register.php'; ?>