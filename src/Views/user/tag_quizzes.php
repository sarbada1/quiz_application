<main class="tag-quiz-page">


    <section class="tag-header">
        <div class="tag-info">
            <div class="tag-icon">
                <i class="<?= $tag['icon'] ?? 'fas fa-tags' ?>"></i>
            </div>
            <div class="tag-details">
                <h1><?= htmlspecialchars($tag['name']) ?> Quizzes</h1>
                <p><?= htmlspecialchars($tag['description'] ?? 'Test your knowledge on ' . $tag['name']) . ' quizzes' ?></p>
            </div>
        </div>
    </section>

    <section class="category-list">
        <?php if (empty($categories)): ?>
            <div class="alert alert-info">
                <h4><i class="fas fa-info-circle"></i> No Questions Available</h4>
                <p>There are currently no questions available for the "<?= htmlspecialchars($tag['name']) ?>" tag.</p>
                <p>Try exploring other tags or check back later.</p>
            </div>
        <?php else: ?>
            <div class="category-accordion">
                <?php foreach ($categories as $category): ?>
                    <div class="category-item">
                        <div class="category-header">
                            <div class="category-info">
                                <h2><?= htmlspecialchars($category['name']) ?></h2>
                                <span class="question-count">
                                    <?php if ($category['question_count'] > 0): ?>
                                        <span class='badge badge-primary text-dark'><?= $category['question_count'] ?> Direct Questions</span>
                                    <?php endif; ?>
                                    <?php if ($category['total_questions'] > 0): ?>
                                        <span class='badge badge-info text-dark'><?= $category['total_questions'] ?> Total Questions</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <?php if ($category['total_questions'] > 0): ?>
                                <div class="category-actions">
                                    <a href="<?= $url('quiz/category/' . $category['id']) ?>?tag=<?= $tag['id'] ?>" class="btn-primary take-quiz-btn">
                                        Take <?= htmlspecialchars($category['name']) ?> Quiz
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($category['children'])): ?>
                            <div class="subcategories">
                                <h3>Topics with Questions</h3>
                                <div class="subcategory-list">
                                    <?php foreach ($category['children'] as $child): ?>
                                        <?php if ($child['question_count'] > 0): ?>
                                            <div class="subcategory-item">
                                                <div class="subcategory-name"><?= htmlspecialchars($child['name']) ?></div>
                                                <div class="subcategory-meta">
                                                    <span class="question-count"><?= $child['question_count'] ?> Questions</span>
                                                    <a href="<?= $url('quiz/category/' . $child['id']) ?>?tag=<?= $tag['id'] ?>" class="take-quiz-btn">
                                                        Take Quiz
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<style>
    .tag-quiz-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .breadcrumb-container {
        margin-bottom: 20px;
    }

    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }

    .breadcrumb-item a {
        color: #0056b3;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #6c757d;
    }

    .tag-header {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .tag-info {
        display: flex;
        align-items: center;
    }

    .tag-icon {
        font-size: 48px;
        margin-right: 20px;
        color: #0056b3;
        width: 80px;
        height: 80px;
        background-color: rgba(0, 86, 179, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .tag-details h1 {
        margin-top: 0;
        margin-bottom: 10px;
        font-size: 32px;
        color: #333;
    }

    .tag-details p {
        margin-bottom: 0;
        color: #6c757d;
        font-size: 16px;
    }

    .category-list {
        margin-bottom: 40px;
    }

    .category-accordion {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .category-item {
        margin-bottom: 15px;
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
    }

    .category-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        cursor: pointer;
    }

    .category-header:hover {
        background-color: #e9ecef;
    }

    .category-info h2 {
        margin: 0;
        font-size: 20px;
        color: #333;
    }

    .question-count {
        color: #6c757d;
        font-size: 14px;
        margin-top: 5px;
        display: inline-block;
    }

    .category-actions {
        display: flex;
        gap: 10px;
    }

    .take-quiz-btn {
        display: inline-block;
        background-color: #0056b3;
        color: #fff;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: background-color 0.2s;
    }

    .take-quiz-btn:hover {
        background-color: #004494;
        color: #fff;
        text-decoration: none;
    }

    .quiz-unavailable {
        display: inline-block;
        background-color: #e9ecef;
        color: #6c757d;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 500;
        font-size: 14px;
    }

    .subcategories {
        padding: 20px;
        background-color: #fff;
        border-top: 1px solid #e9ecef;
    }

    .subcategories h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 18px;
        color: #495057;
    }

    .subcategory-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
    }

    .subcategory-item {
        background-color: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .subcategory-name {
        font-weight: 500;
        color: #333;
    }

    .subcategory-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 5px;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .tag-icon {
            font-size: 36px;
            width: 60px;
            height: 60px;
        }

        .tag-details h1 {
            font-size: 24px;
        }

        .category-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .category-actions {
            width: 100%;
        }

        .btn-primary {
            width: 100%;
            text-align: center;
        }

        .subcategory-list {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle subcategories visibility when category header is clicked
        const categoryHeaders = document.querySelectorAll('.category-header');
        categoryHeaders.forEach(header => {
            header.addEventListener('click', function(e) {
                // Only toggle if not clicking on the take quiz button
                if (!e.target.closest('.take-quiz-btn')) {
                    const subcategories = this.nextElementSibling;
                    if (subcategories && subcategories.classList.contains('subcategories')) {
                        subcategories.style.display = subcategories.style.display === 'none' ? 'block' : 'none';
                    }
                }
            });
        });
        
        // Add tooltips to parent categories with only indirect questions
        const parentCategories = document.querySelectorAll('.category-item');
        parentCategories.forEach(category => {
            const directQuestionBadge = category.querySelector('.badge-primary');
            const totalQuestionsBadge = category.querySelector('.badge-info');
            
            if (!directQuestionBadge && totalQuestionsBadge) {
                // This parent has only indirect questions
                category.classList.add('parent-only-indirect');
                
                const quizBtn = category.querySelector('.take-quiz-btn');
                if (quizBtn) {
                    quizBtn.setAttribute('title', 'This quiz will include questions from all subcategories');
                }
            }
        });
    });
</script>