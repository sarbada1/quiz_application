<div class="quiz-info-wrapper py-5">
    <div class="container">


        <!-- Quiz Header -->
        <div class="quiz-header-banner mb-5">
            <div class="quiz-header-content">
                <div class="quiz-badge-container">
                    <div class="quiz-badge">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
                <div class="quiz-title-container">
                    <h1><?= htmlspecialchars($category['name'] ?? $quiz['title'] ?? 'Quiz') ?></h1>
                    <div class="quiz-meta">
                        <?php if (isset($category['parent_name']) && !empty($category['parent_name'])): ?>
                            <div class="quiz-meta-item">
                                <i class="fas fa-folder-open"></i>
                                <span><?= htmlspecialchars($category['parent_name']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="quiz-meta-item">
                            <i class="fas fa-question-circle"></i>
                            <span><?= $quiz['question_count'] ?? $category['total_questions'] ?? $category['question_count'] ?? 0 ?> Questions</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Quiz Description -->
            <div class="col-lg-8 mb-4">
                <div class="content-card quiz-description">
                    <div class="content-card-header">
                        <h2><i class="fas fa-info-circle me-2"></i> About This Quiz</h2>
                    </div>
                    <div class="content-card-body">
                        <p><?= nl2br(htmlspecialchars($quiz['description'] ?? "Test your knowledge of " . htmlspecialchars($category['name'] ?? 'this subject') . " with this comprehensive quiz.")) ?></p>


                    </div>
                </div>


            </div>

            <!-- Quiz Start Section -->
            <div class="col-lg-4 mb-4">
                <div class="content-card start-quiz-card">
                    <div class="content-card-header">
                        <h2><i class="fas fa-play-circle me-2"></i> Start Quiz</h2>
                    </div>
                    <div class="content-card-body">
                        <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
                            <form id="quizPreferenceForm">
                                <div class="form-group mb-4">
                                    <label for="questionCount">Number of Questions</label>
                                    <select id="questionCount" name="count" class="form-control" required>
                                        <?php
                                        // Get the maximum number of questions available
                                        $maxQuestions = $quiz['question_count'] ?? $category['total_questions'] ?? $category['question_count'] ?? 30;

                                        // Create dynamic options based on the max questions available
                                        $options = [];

                                        // Always include standard small options if possible
                                        if ($maxQuestions >= 5) $options[] = 5;
                                        if ($maxQuestions >= 10) $options[] = 10;
                                        if ($maxQuestions >= 15) $options[] = 15;
                                        if ($maxQuestions >= 20) $options[] = 20;

                                        // For larger question sets, add increments of 10
                                        $increment = 10;
                                        $nextOption = 30;

                                        while ($nextOption <= $maxQuestions) {
                                            if (!in_array($nextOption, $options)) {
                                                $options[] = $nextOption;
                                            }
                                            $nextOption += $increment;
                                        }

                                        // Add the exact max if it's not already included
                                        if (!in_array($maxQuestions, $options) && $maxQuestions > 20) {
                                            $options[] = $maxQuestions;
                                        }

                                        // Sort options in ascending order
                                        sort($options);

                                        // Output all options
                                        foreach ($options as $value):
                                        ?>
                                            <option value="<?= $value ?>" <?= $value == 10 ? 'selected' : '' ?>><?= $value ?> Questions</option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                                <button type="button" onclick="startQuiz()" class="start-quiz-btn">
                                    Start Quiz <i class="fas fa-arrow-right"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="login-required">
                                <div class="login-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <h3>Login Required</h3>
                                <p>Please log in to start this quiz</p>
                                <button class="login-btn" id="startQuizzing">
                                    <i class="fas fa-sign-in-alt"></i> Login to Continue
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/auth/login.php'; ?>
<?php include __DIR__ . '/auth/register.php'; ?>

<style>
    .quiz-info-wrapper {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding-top: 80px;
    }

    .breadcrumb {
        background-color: #fff;
        border-radius: 8px;
        padding: 12px 20px;
        margin-bottom: 0;
    }

    .breadcrumb-item a {
        color: #4a6cf7;
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb-item.active {
        color: #495057;
        font-weight: 600;
    }

    /* Quiz Header Banner */
    .quiz-header-banner {
        background: linear-gradient(135deg, #4a6cf7 0%, #2451e6 100%);
        border-radius: 12px;
        padding: 50px 40px;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(74, 108, 247, 0.2);
    }

    .quiz-header-banner::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(50%, -50%);
    }

    .quiz-header-content {
        display: flex;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .quiz-badge-container {
        margin-right: 30px;
    }

    .quiz-badge {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        width: 90px;
        height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quiz-badge i {
        font-size: 2.5rem;
    }

    .quiz-title-container h1 {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: white;
    }

    .quiz-meta {
        display: flex;
        gap: 20px;
    }

    .quiz-meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* Content Cards */
    .content-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .content-card-header {
        padding: 20px 25px;
        border-bottom: 1px solid #f0f0f0;
    }

    .content-card-header h2 {
        font-size: 1.3rem;
        font-weight: 600;
        color: #212529;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .content-card-header h2 i {
        color: #4a6cf7;
        margin-right: 10px;
    }

    .content-card-body {
        padding: 25px;
        flex: 1;
    }

    .content-card-body p {
        color: #6c757d;
        line-height: 1.6;
        font-size: 1rem;
    }

    /* Subcategory Section */
    .subcategory-section {
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }

    .subcategory-section h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #343a40;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .subcategory-section h3 i {
        color: #4a6cf7;
        margin-right: 10px;
    }

    .subcategory-list {
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
    }

    .subcategory-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #eaeaea;
    }

    .subcategory-item:last-child {
        border-bottom: none;
    }

    .subcategory-name {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .subcategory-name i {
        color: #28a745;
    }

    .subcategory-name span {
        font-weight: 500;
        color: #343a40;
    }

    .subcategory-count {
        background-color: #e9ecef;
        color: #495057;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 50px;
    }

    /* Instructions List */
    .instruction-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .instruction-list li {
        display: flex;
        align-items: flex-start;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f0f0f0;
    }

    .instruction-list li:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .instruction-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .time-icon {
        background-color: #fff3cd;
        color: #ffc107;
    }

    .tasks-icon {
        background-color: #cfe2ff;
        color: #0d6efd;
    }

    .back-icon {
        background-color: #f8d7da;
        color: #dc3545;
    }

    .results-icon {
        background-color: #d1e7dd;
        color: #198754;
    }

    .instruction-text {
        font-size: 1rem;
        color: #495057;
        padding-top: 5px;
    }

    /* Start Quiz Card */
    .start-quiz-card {
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #f0f0f0;
    }

    .start-quiz-card .content-card-header {
        background-color: #f8f9fa;
        text-align: center;
    }

    .start-quiz-card .content-card-header h2 {
        justify-content: center;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: #343a40;
        margin-bottom: 10px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        font-size: 1rem;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #4a6cf7;
        box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.2);
        outline: none;
    }

    .start-quiz-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 14px;
        background: linear-gradient(to right, #4a6cf7, #2451e6);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        gap: 10px;
    }

    .start-quiz-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
    }

    .start-quiz-btn i {
        font-size: 0.9rem;
    }

    /* Login Required */
    .login-required {
        text-align: center;
        padding: 20px 0;
    }

    .login-icon {
        width: 70px;
        height: 70px;
        background: #f8f9fa;
        border-radius: 50%;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-icon i {
        font-size: 2rem;
        color: #6c757d;
    }

    .login-required h3 {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: #343a40;
    }

    .login-required p {
        color: #6c757d;
        margin-bottom: 25px;
    }

    .login-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 25px;
        background: linear-gradient(to right, #4a6cf7, #2451e6);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .login-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
    }

    /* Media Queries */
    @media (max-width: 991px) {
        .quiz-info-wrapper {
            padding-top: 60px;
        }

        .start-quiz-card {
            margin-top: 30px;
        }
    }

    @media (max-width: 767px) {
        .quiz-header-banner {
            padding: 30px;
        }

        .quiz-header-content {
            flex-direction: column;
            text-align: center;
        }

        .quiz-badge-container {
            margin-right: 0;
            margin-bottom: 20px;
        }

        .quiz-title-container h1 {
            font-size: 1.8rem;
        }

        .quiz-meta {
            justify-content: center;
            flex-wrap: wrap;
        }
    }

    @media (max-width: 576px) {
        .quiz-header-banner {
            padding: 25px 20px;
        }

        .quiz-title-container h1 {
            font-size: 1.5rem;
        }

        .quiz-badge {
            width: 70px;
            height: 70px;
        }

        .quiz-badge i {
            font-size: 2rem;
        }

        .content-card-header,
        .content-card-body {
            padding: 20px;
        }

        .instruction-icon {
            width: 35px;
            height: 35px;
        }
    }
</style>

<script>
    function startQuiz() {
        const count = document.getElementById('questionCount').value;
        const categoryId = '<?= $category_id ?? $category['id'] ?? "" ?>';
        const tagId = '<?= $tag_id ?? $tag['id'] ?? "" ?>';

        console.log("Starting quiz with:", {
            count,
            categoryId,
            tagId
        });

        if (categoryId) {
            // Use window.location.origin to get the base URL
            const baseUrl = window.location.origin;
            let url = baseUrl + '/quiz/category/' + categoryId + '/start/' + count;

            // Add tag parameter if available
            if (tagId) {
                url += '?tag=' + tagId;
            }

            console.log("Navigating to:", url);
            window.location.href = url;
        } else {
            console.error("Error: No category ID available");
        }
    }

    document.getElementById('startQuizzing')?.addEventListener('click', function() {
        document.getElementById('loginModalBtn').click();
    });
</script>