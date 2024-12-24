<!-- index.php -->
<main>
    <section class="hero">
        <div class="hero-content">
            <h1>Master Your Knowledge</h1>
            <p>Take quizzes, practice mock tests, and track your progress</p>
            <div class="hero-buttons">
                <a href="/quiz/configure" class="btn-primary">Start Quiz</a>
                <a href="/test" class="btn-secondary">Try Mock Test</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="/src/Views/user/img/bg.png" alt="Quiz illustration">
        </div>
    </section>

    <section class="categories-section">
        <h2>Explore Categories</h2>
        <div class="category-grid">
            <?php foreach ($parentCategories as $category): ?>
                <div class="category-card">
                    <div class="category-header">
                        <i class="<?= $category['icon'] ?? 'fas fa-book' ?>"></i>
                        <h3><?= htmlspecialchars($category['name']) ?></h3>
                    </div>
                    <p><?= htmlspecialchars($category['description'] ?? 'Explore ' . htmlspecialchars($category['name']) . ' topics and test your knowledge.') ?></p>
                    <?php if (!empty($category['children'])): ?>
                        <div class="subcategories collapsed">
                            <?php foreach (array_slice($category['children'], 0, 3) as $child): ?>
                                <a href="/category/<?= htmlspecialchars($child['slug'] ?? '') ?>" class="subcategory-link">
                                    <?= htmlspecialchars($child['name']) ?>
                                </a>
                            <?php endforeach; ?>

                            <?php if (count($category['children']) > 3): ?>
                                <button class="show-more" data-category="<?= $category['id'] ?>">
                                    Show More
                                </button>
                                <div class="hidden-subcategories" id="category-<?= $category['id'] ?>">
                                    <?php foreach (array_slice($category['children'], 3) as $child): ?>
                                        <a href="/category/<?= htmlspecialchars($child['slug'] ?? '') ?>" class="subcategory-link">
                                            <?= htmlspecialchars($child['name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <a href="/category/<?= $category['slug'] ?>" class="btn-explore">
                        Explore <?= htmlspecialchars($category['name']) ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php include __DIR__ . '/auth/login.php'; ?>
<?php include __DIR__ . '/auth/register.php'; ?>
<style>
    .hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 2rem 8%;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 60vh;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.1) 0%, rgba(41, 128, 185, 0.05) 100%);
        z-index: 1;
    }

    .hero-content {
        flex: 1;
        max-width: 500px;
        position: relative;
        z-index: 2;
        animation: slideIn 0.8s ease-out;
    }

    .hero-content h1 {
        font-size: 2.8rem;
        margin-bottom: 1rem;
        color: #2c3e50;
        line-height: 1.2;
        font-weight: 700;
    }

    .hero-content p {
        font-size: 1.1rem;
        color: #505d6b;
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }

    .hero-buttons {
        display: flex;
        gap: 1rem;
    }

    .btn-primary,
    .btn-secondary {
        padding: 0.8rem 1.8rem;
        border-radius: 30px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #3498db;
        color: white;
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
    }

    .btn-secondary {
        background: white;
        color: #3498db;
        border: 2px solid #3498db;
    }

    .btn-primary:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }

    .btn-secondary:hover {
        background: #f8f9fa;
        transform: translateY(-2px);
    }

    .hero-image {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        z-index: 2;
        animation: fadeIn 1s ease-out;
    }

    .hero-image img {
        max-width: 90%;
        height: auto;
        filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.1));
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .hero {
            flex-direction: column;
            text-align: center;
            padding: 2rem 5%;
        }

        .hero-content {
            margin-bottom: 2rem;
        }

        .hero-buttons {
            justify-content: center;
        }

        .hero-content h1 {
            font-size: 2.2rem;
        }
    }

    .categories-section {
        padding: 4rem 8%;
        background: #fff;
    }

    .categories-section h2 {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 3rem;
        color: #2c3e50;
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .category-card {
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .category-card:hover {
        transform: translateY(-5px);
    }

    .category-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .category-header i {
        font-size: 2rem;
        color: #3498db;
    }

    .category-header h3 {
        font-size: 1.5rem;
        color: #2c3e50;
    }

    .subcategories {
        margin: 1rem 0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .subcategory-link {
        color: #6c757d;
        text-decoration: none;
        padding: 0.5rem;
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .subcategory-link:hover {
        background: #f8f9fa;
        color: #3498db;
    }

    .show-more {
        background: none;
        border: none;
        color: #3498db;
        cursor: pointer;
        padding: 0.5rem;
        margin-top: 0.5rem;
    }

    .hidden-subcategories {
        display: none;
    }

    .btn-explore {
        display: inline-block;
        padding: 0.8rem 1.5rem;
        background: #3498db;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        margin-top: 1rem;
        transition: background 0.3s ease;
    }

    .btn-explore:hover {
        background: #2980b9;
    }
</style>

<script>
    document.querySelectorAll('.show-more').forEach(button => {
        button.addEventListener('click', () => {
            const categoryId = button.dataset.category;
            const hiddenContent = document.getElementById(`category-${categoryId}`);

            if (hiddenContent.style.display === 'none') {
                hiddenContent.style.display = 'block';
                button.textContent = 'Show Less';
            } else {
                hiddenContent.style.display = 'none';
                button.textContent = 'Show More';
            }
        });
    });
</script>