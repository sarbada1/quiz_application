 


<div class="test-mock-container">
    <?php if (!empty($mocktest)) : ?>
        <div class="test-details">
            <div class="test-header">
                <h2><?= htmlspecialchars($mocktest['title']) ?></h2>
                <?php if (!empty($mocktest['description'])): ?>
                    <p class="test-description"><?= htmlspecialchars($mocktest['description']) ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($sets)): ?>
                <div class="sets-container">
                    <div class="section-header">
                        <h3>Available Sets</h3>
                        <p class="section-subtitle">Select a set to begin your practice</p>
                    </div>
                    <div class="sets-grid">
                        <?php foreach ($sets as $set): ?>
                            <div class="set-card <?= $set['status'] !== 'published' ? 'disabled-card' : '' ?>">
                                <div class="set-card-header">
                                    <h4><?= htmlspecialchars($set['set_name']) ?></h4>
                                    <?php if (!empty($set['questions_count'])): ?>
                                        <span class="question-count"><?= $set['questions_count'] ?> questions</span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($set['description'])): ?>
                                    <p class="set-description"><?= htmlspecialchars($set['description']) ?></p>
                                <?php endif; ?>
                                
                                <div class="set-card-footer">
                                    <?php if ($isLoggedIn): ?>
                                        <?php if ($set['status'] === 'published'): ?>
                                            <a href="<?= $url('mocktest/' . $set['id']) ?>" 
                                               class="start-exam-btn"
                                               onclick="return confirm('This will start the timer. Are you ready to begin?')">
                                                Start Now
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        <?php else: ?>
                                            <div class="set-status">
                                                <i class="fas fa-clock"></i>
                                                <span>Coming Soon</span>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button class="login-required-btn" id="startQuiz">
                                            <i class="fas fa-lock"></i> Login Required
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-sets-message">
                    <i class="fas fa-info-circle"></i>
                    <p>No sets available for this test yet.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="no-test-message">
            <i class="fas fa-exclamation-triangle"></i>
            <p>Test not found.</p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/auth/login.php'; ?>
<?php include __DIR__ . '/auth/register.php'; ?>

<style>
    .test-mock-container {
        max-width: 1200px;
        margin: 9rem auto;
        padding: 0 1.5rem;
    }

    .test-header {
        margin-bottom: 2.5rem;
        text-align: center;
    }

    .test-header h2 {
        font-size: 2.2rem;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #3498db, #2c3e50);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        display: inline-block;
    }

    .test-description {
        color: #7f8c8d;
        font-size: 1.1rem;
        max-width: 800px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .section-header {
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .section-header h3 {
        font-size: 1.5rem;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }

    .section-subtitle {
        color: #7f8c8d;
        font-size: 0.95rem;
    }

    .sets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .set-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        transition: all 0.3s ease;
        border: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .set-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        border-color: #3498db;
    }

    .disabled-card {
        opacity: 0.8;
        border-color: #f1f1f1;
    }

    .disabled-card:hover {
        transform: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border-color: #f1f1f1;
        cursor: not-allowed;
    }

    .set-card-header {
        margin-bottom: 1rem;
        border-bottom: 1px solid #f1f1f1;
        padding-bottom: 0.75rem;
    }

    .set-card-header h4 {
        font-size: 1.2rem;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .question-count {
        display: inline-block;
        background: #f8f9fa;
        color: #7f8c8d;
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
        border-radius: 50px;
    }

    .set-description {
        color: #7f8c8d;
        font-size: 0.95rem;
        line-height: 1.5;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .set-card-footer {
        margin-top: auto;
    }

    .start-exam-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        width: 100%;
    }

    .start-exam-btn:hover {
        background: linear-gradient(135deg, #2980b9, #3498db);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(41, 128, 185, 0.3);
    }

    .start-exam-btn i {
        margin-left: 0.5rem;
        font-size: 0.9rem;
    }

    .login-required-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        width: 100%;
    }

    .login-required-btn:hover {
        background: linear-gradient(135deg, #495057, #6c757d);
    }

    .login-required-btn i {
        margin-right: 0.5rem;
    }

    .set-status {
        display: inline-flex;
        align-items: center;
        color: #7f8c8d;
        font-size: 0.9rem;
        padding: 0.75rem 0;
    }

    .set-status i {
        margin-right: 0.5rem;
        color: #f39c12;
    }

    .no-sets-message, .no-test-message {
        text-align: center;
        padding: 3rem;
        background: #f8f9fa;
        border-radius: 8px;
        color: #7f8c8d;
    }

    .no-sets-message i, .no-test-message i {
        font-size: 2rem;
        color: #3498db;
        margin-bottom: 1rem;
    }

    .no-sets-message p, .no-test-message p {
        font-size: 1.1rem;
        margin-top: 0.5rem;
    }

    @media (max-width: 768px) {
        .test-header h2 {
            font-size: 1.8rem;
        }
        
        .test-description {
            font-size: 1rem;
        }
        
        .sets-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    // Make login button trigger the login modal
    document.querySelectorAll('.login-required-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Assuming you have a function to show login modal
            // This should match your existing login modal trigger
            showLoginModal();
        });
    });

    // Function to show login modal (should match your existing implementation)
    function showLoginModal() {
        // Your existing login modal trigger code here
        // For example:
        // document.getElementById('loginModal').style.display = 'block';
    }
</script>
