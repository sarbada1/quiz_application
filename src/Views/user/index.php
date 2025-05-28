<main>
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Master Your Knowledge</h1>
                <p class="hero-subtitle">Take quizzes, practice mock tests, and track your progress</p>
                <div class="hero-buttons">
                    <!-- <a href="<?= $url('quiz') ?>" class="btn-primary">Start Quiz</a> -->
                    <a href="<?= $url('test') ?>" class="btn-secondary">Try Mock Test</a>
                    <a href="<?= $url('previous-year-quizzes') ?>" class="btn btn-secondary">Previous Year Quizzes</a>
                </div>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number">1000+</span>
                    <span class="stat-label">Questions</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">50+</span>
                    <span class="stat-label">Categories</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">10K+</span>
                    <span class="stat-label">Users</span>
                </div>
            </div>
        </div>
        <div class="hero-image">
            <img src="<?= $url('src/Views/user/img/bg.png') ?>" alt="Quiz illustration" class="floating">
            <div class="hero-dots"></div>
        </div>
    </section>

    <!-- Rest of your existing code remains unchanged -->
     <section class="categories-section">
        <h2>Available Quizzes</h2>
        <div class="category-grid">
            <?php foreach ($tagsWithQuestions as $tag): ?>
                <div class="category-card">
                    <div class="category-header">
                        <i class="<?= $tag['icon'] ?? 'fas fa-book' ?>"></i>
                        <h3><?= htmlspecialchars($tag['name']) ?> Quiz</h3>
                    </div>
                    <p><?= htmlspecialchars($tag['description'] ?? 'Take quizzes and test your knowledge.') ?></p>
                
                    <a href="<?= $url('tag/' . htmlspecialchars($tag['slug'] ?? '')) ?>" class="btn-explore">
                        Explore <?= htmlspecialchars($tag['name']) ?> Quizzes
                    </a>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($tagsWithQuestions)): ?>
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <p>No quizzes available at the moment. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<style>
    /* Updated Hero Section Styles */
    .hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 4rem 8%;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ed 100%);
        min-height: 80vh;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(52, 152, 219, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
        z-index: 1;
        animation: pulse 15s infinite alternate;
    }

    .hero-content {
        flex: 1;
        max-width: 600px;
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        gap: 2.5rem;
    }

    .hero-text {
        animation: slideIn 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    .hero-content h1 {
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
        color: #2c3e50;
        line-height: 1.2;
        font-weight: 800;
        background: linear-gradient(90deg, #2c3e50, #4a6491);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        letter-spacing: -0.5px;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        color: #5a6a7f;
        margin-bottom: 2rem;
        line-height: 1.6;
        max-width: 90%;
        position: relative;
    }

    .hero-subtitle::after {
        content: '';
        position: absolute;
        bottom: -1rem;
        left: 0;
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, #3498db, #9b59b6);
        border-radius: 2px;
    }

    .hero-buttons {
        display: flex;
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .btn-primary,
    .btn-secondary {
        padding: 0.9rem 1.8rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .btn-primary {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        border: none;
    }

    .btn-secondary {
        background: white;
        color: #3498db;
        border: 2px solid #3498db;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(52, 152, 219, 0.2);
    }

    .btn-secondary:hover {
        background: #f8f9fa;
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(52, 152, 219, 0.1);
    }

    .hero-stats {
        display: flex;
        gap: 2rem;
        margin-top: 1rem;
        animation: fadeInUp 1s 0.3s both;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #3498db;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: 0.3rem;
    }

    .hero-image {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .hero-image img {
        max-width: 100%;
        height: auto;
        position: relative;
        z-index: 3;
        animation: float 6s ease-in-out infinite;
    }

    .hero-dots {
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(52, 152, 219, 0.15) 0%, rgba(52, 152, 219, 0) 70%);
        border-radius: 50%;
        z-index: 1;
        animation: pulse 8s infinite alternate;
    }

    /* Animations */
    @keyframes float {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-20px);
        }
    }

    @keyframes pulse {
        0% {
            transform: scale(0.95);
            opacity: 0.8;
        }
        100% {
            transform: scale(1.05);
            opacity: 1;
        }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-40px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .hero-content h1 {
            font-size: 3rem;
        }
    }

    @media (max-width: 992px) {
        .hero {
            flex-direction: column;
            text-align: center;
            padding: 4rem 5%;
            gap: 3rem;
        }

        .hero-content {
            align-items: center;
            text-align: center;
        }

        .hero-subtitle {
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-subtitle::after {
            left: 50%;
            transform: translateX(-50%);
        }

        .hero-buttons {
            justify-content: center;
        }

        .hero-stats {
            justify-content: center;
        }

        .stat-item {
            align-items: center;
        }

        .hero-image {
            width: 80%;
            margin: 0 auto;
        }
    }

    @media (max-width: 768px) {
        .hero-content h1 {
            font-size: 2.5rem;
        }

        .hero-buttons {
            flex-direction: column;
            gap: 1rem;
            width: 100%;
        }

        .btn-primary,
        .btn-secondary {
            width: 100%;
            padding: 1rem;
        }

        .hero-stats {
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .stat-number {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 480px) {
        .hero-content h1 {
            font-size: 2rem;
        }

        .hero-subtitle {
            font-size: 1.1rem;
        }

        .stat-number {
            font-size: 1.5rem;
        }
    }

    /* Rest of your existing styles remain unchanged */
    .categories-section {
        /* ... your existing categories styles ... */
    }
</style>

<script>
    // Your existing script remains unchanged
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
        padding: 0.8rem 1rem;
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

    /* Responsive navbar */
    @media (max-width: 768px) {
        #navbar {
            flex-direction: column;
            padding: 10px;
        }

        .sidebar-nav ul li {
            display: block;
            text-align: center;
        }

        .sidebar-nav ul li ul.dropdown {
            position: static;
            width: 100%;
            box-shadow: none;
        }
    }

    /* Responsive main content */
    @media (max-width: 1024px) {
        .main-content {
            grid-template-columns: 1fr;
            margin-top: 120px;
        }

        .question-palette {
            position: relative;
            top: 0;
            order: -1;
        }

        .palette-buttons {
            grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
        }
    }

    /* Responsive header */
    @media (max-width: 768px) {
        .header {
            flex-direction: column;
            padding: 10px;
            height: auto;
        }

        .timer-section {
            width: 100%;
            margin: 10px 0;
        }

        .submit-btn {
            width: 100%;
            margin-top: 10px;
        }
    }

    /* Responsive quiz container */
    @media (max-width: 768px) {
        .test-container {
            margin: 100px auto 20px;
            padding: 10px;
        }

        .question-card {
            padding: 15px;
        }

        .option {
            padding: 12px;
        }
    }

    /* Responsive review section */
    @media (max-width: 768px) {
        .review-container {
            padding: 10px;
        }

        .review-item {
            margin: 5px 0;
        }

        .answers-list {
            padding: 5px;
        }
    }

    /* Performance modal responsive */
    @media (max-width: 768px) {
        .performance-modal .modal-content {
            width: 95%;
            padding: 15px;
        }

        .performance-stats {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
            gap: 10px;
        }

        .action-buttons button {
            width: 100%;
        }
    }

    /* Timer responsive */
    @media (max-width: 768px) {
        .timer {
            font-size: 1.2rem;
        }
    }

    /* Question navigation responsive */
    @media (max-width: 480px) {
        .palette-btn {
            width: 35px;
            height: 35px;
            font-size: 12px;
        }

        .category-section {
            padding: 10px;
        }
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
