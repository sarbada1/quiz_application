<div class="test-mock-container">
    <?php if (!empty($mocktest)) : ?>
        <div class="test-details">
            <h2><?= htmlspecialchars($mocktest['title']) ?></h2>

            <?php if (!empty($sets)): ?>
                <div class="sets-container">
                    <h3>Available Sets</h3>
                    <div class="sets-grid">
                        <?php foreach ($sets as $set): ?>
                            <div class="set-card">
                                <h4><?= htmlspecialchars($set['set_name']) ?></h4>
                                <?php if ($isLoggedIn): ?>
                                    <?php if ($set['status'] === 'published'): ?>
                                        <a href="<?= $url('mocktest/' . $set['id']) ?>"                                            class="start-exam-btn"
                                            onclick="return confirm('Are you sure to start this set?')">
                                            Start Set
                                        </a>
                                    <?php else: ?>
                                        <p class="set-status">Set not published yet</p>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <button class="login-required-btn" id="startQuiz">Login Required</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    <?php else: ?>
        <p class="no-test">Test not found.</p>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/auth/login.php'; ?>
<?php include __DIR__ . '/auth/register.php'; ?>
<style>
    .sets-container {
        margin-top: 20px;
    }

    .sets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 15px;
    }

    .set-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .set-card h4 {
        margin-bottom: 15px;
        color: #2c3e50;
    }

    .set-status {
        color: #7f8c8d;
        font-style: italic;
    }
</style>