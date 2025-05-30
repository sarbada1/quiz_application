<main>
    <!-- hero section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Master Your Knowledge</h1>
                <p class="hero-subtitle">Take quizzes, practice mock tests, and track your progress</p>
                <div class="hero-buttons">
                    <a href="<?= $url('test') ?>" class="btn-secondary">Try Mock Test</a>
                    <a href="<?= $url('previous-year-quizzes') ?>" class="btn btn-secondary">Previous Year Quizzes</a>
                </div>
            </div>
        </div>
        <div class="hero-image">
            <img src="<?= $url('src/Views/user/img/bg.png') ?>" alt="Quiz illustration" class="floating">
            <div class="hero-dots"></div>
        </div>
    </section>

    <!-- tags sections -->
    <section class="categories-section">
        <h2>Quizzes Categories</h2>
        <div class="category-grid">
            <?php foreach ($tagsWithQuestions as $tag): ?>
                <div class="category-card">
                    <div class="category-header">
                        <i class="<?= $tag['icon'] ?? 'fas fa-book' ?>"></i>
                        <h3><?= htmlspecialchars($tag['name']) ?> Quiz</h3>
                    </div>
                    <p><?= htmlspecialchars($tag['description'] ?? 'Take quizzes and test your knowledge.') ?></p>
                    <a href="<?= $url('tag/' . htmlspecialchars($tag['slug'] ?? '')) ?>" class="btn-explore">
                        Explore
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

    <!-- mock test section --> 
    <section class='categories-section'>
        <h2>Mock Test</h2>
        <div class="tst-main-container">
            <!-- Category Tabs -->
            <div class="tst-category-tabs">
                <div class="tst-tab-header">
                    <button class="tst-tab-btn active" data-tab="all">All Programs</button>
                    <?php
                    $uniqueTags = [];
                    foreach($mockquiz as $quiz) {
                        $tagSlug = $quiz['t_slug'];
                        $tagName = $quiz['t_name'];
                        if (!isset($uniqueTags[$tagSlug])) {
                            $uniqueTags[$tagSlug] = $tagName;
                        }
                    }
                    
                    foreach($uniqueTags as $slug => $name) {
                        echo '<button class="tst-tab-btn" data-tab="' . htmlspecialchars($slug) . '">' . htmlspecialchars($name) . '</button>';
                    }
                    ?>
                </div>
                
                <!-- All Programs Tab -->
                <div class="tst-tab-content active" id="tab-all">
                    <div class="tst-grid-wrapper">
                        <?php
                        foreach($mockquiz as $quiz) {
                            echo '<div class="tst-program-card" data-category="' . htmlspecialchars($quiz['t_slug']) . '">';
                            echo '<h3 class="tst-card-title">' . htmlspecialchars($quiz['q_title']) . '</h3>';
                            echo '<div class="tst-card-actions">';
                            echo '<a href="'.$url('test/' . $quiz['q_slug']).'" class="tst-btn tst-btn-primary">' . htmlspecialchars(ucfirst($quiz['q_type'])) . ' Test</a>';
                            echo '</div>';
                            echo '</div>';
                        }
                        
                        if (empty($mockquiz)) {
                            echo '<div class="tst-empty-state">';
                            echo '<div class="tst-empty-icon">';
                            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">';
                            echo '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>';
                            echo '</svg>';
                            echo '</div>';
                            echo '<p class="tst-empty-text">No mock tests available</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
                
                <?php
                foreach($uniqueTags as $slug => $name) {
                    echo '<div class="tst-tab-content" id="tab-' . htmlspecialchars($slug) . '">';
                    echo '<div class="tst-grid-wrapper">';
                    
                    $filteredQuizzes = array_filter($mockquiz, function($quiz) use ($slug) {
                        return $quiz['t_slug'] === $slug;
                    });
                    
                    foreach($filteredQuizzes as $quiz) {
                        echo '<div class="tst-program-card">';
                        echo '<h3 class="tst-card-title">' . htmlspecialchars($quiz['q_title']) . '</h3>';
                        echo '<div class="tst-card-actions">';
                        echo '<a href="'.$url('test/' . $quiz['q_slug']).'" class="tst-btn tst-btn-primary">' . htmlspecialchars(ucfirst($quiz['q_type'])) . ' Test</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                    
                    if (empty($filteredQuizzes)) {
                        echo '<div class="tst-empty-state">';
                        echo '<div class="tst-empty-icon">';
                        echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">';
                        echo '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>';
                        echo '</svg>';
                        echo '</div>';
                        echo '<p class="tst-empty-text">No ' . htmlspecialchars($name) . ' mock tests available</p>';
                        echo '</div>';
                    }
                    
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Previous year question -->
    <section class="categories-section">
        <h2>Previous Year Questions</h2>
        <div class="tst-main-container">
            <!-- Category Tabs -->
            <div class="tst-category-tabs">
                <div class="tst-tab-header">
                    <button class="tst-tab-btn active" data-tab="py-all">All Programs</button>
                    <?php
                    $uniquePYTags = [];
                    foreach($previous_year_quiz as $quiz) {
                        $tagSlug = $quiz['t_slug'];
                        $tagName = $quiz['t_name'];
                        if (!isset($uniquePYTags[$tagSlug])) {
                            $uniquePYTags[$tagSlug] = $tagName;
                        }
                    }
                    
                    foreach($uniquePYTags as $slug => $name) {
                        echo '<button class="tst-tab-btn" data-tab="py-' . htmlspecialchars($slug) . '">' . htmlspecialchars($name) . '</button>';
                    }
                    ?>
                </div>
                
                <!-- All Programs Tab -->
                <div class="tst-tab-content active" id="tab-py-all">
                    <div class="tst-grid-wrapper">
                        <?php
                        foreach($previous_year_quiz as $quiz) {
                            echo '<div class="tst-program-card" data-category="' . htmlspecialchars($quiz['t_slug']) . '">';
                            echo '<h3 class="tst-card-title">' . htmlspecialchars($quiz['q_title']) . '</h3>';
                            echo '<div class="tst-card-actions">';
                            echo '<a href="' . $url('previous-year-quiz/' . $quiz['q_id'])  . '" class="tst-btn tst-btn-primary">' . 
                                 htmlspecialchars(ucfirst(str_replace('_', ' ', $quiz['q_type']))) . '</a>';
                            echo '</div>';
                            echo '</div>';
                        }
                        
                        if (empty($previous_year_quiz)) {
                            echo '<div class="tst-empty-state">';
                            echo '<div class="tst-empty-icon">';
                            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">';
                            echo '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>';
                            echo '</svg>';
                            echo '</div>';
                            echo '<p class="tst-empty-text">No previous year questions available</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
                
                <?php
                foreach($uniquePYTags as $slug => $name) {
                    echo '<div class="tst-tab-content" id="tab-py-' . htmlspecialchars($slug) . '">';
                    echo '<div class="tst-grid-wrapper">';
                    
                    $filteredPYQuizzes = array_filter($previous_year_quiz, function($quiz) use ($slug) {
                        return $quiz['t_slug'] === $slug;
                    });
                    
                    foreach($filteredPYQuizzes as $quiz) {
                        echo '<div class="tst-program-card">';
                        echo '<h3 class="tst-card-title">' . htmlspecialchars($quiz['q_title']) . '</h3>';
                        echo '<div class="tst-card-actions">';
                        echo '<a href="' .  $url('previous-year-quiz/' . $quiz['q_id']) . '" class="tst-btn tst-btn-primary">' . 
                             htmlspecialchars(ucfirst(str_replace('_', ' ', $quiz['q_type']))) . '</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                    
                    if (empty($filteredPYQuizzes)) {
                        echo '<div class="tst-empty-state">';
                        echo '<div class="tst-empty-icon">';
                        echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">';
                        echo '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>';
                        echo '</svg>';
                        echo '</div>';
                        echo '<p class="tst-empty-text">No ' . htmlspecialchars($name) . ' previous year questions available</p>';
                        echo '</div>';
                    }
                    
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>
    
    
    
</main>

<script>
    // Tab switching functionality for both sections
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tabs for both sections
        initTabs('.tst-category-tabs');
        
        function initTabs(containerSelector) {
            const containers = document.querySelectorAll(containerSelector);
            
            containers.forEach(container => {
                const tabButtons = container.querySelectorAll('.tst-tab-btn');
                
                tabButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        // Get the parent container to scope our changes
                        const parentContainer = this.closest('.tst-category-tabs');
                        
                        // Remove active class from all buttons and content in this container
                        parentContainer.querySelectorAll('.tst-tab-btn').forEach(btn => {
                            btn.classList.remove('active');
                        });
                        parentContainer.querySelectorAll('.tst-tab-content').forEach(content => {
                            content.classList.remove('active');
                        });
                        
                        // Add active class to clicked button
                        this.classList.add('active');
                        
                        // Show corresponding content
                        const tabId = this.getAttribute('data-tab');
                        parentContainer.querySelector(`#tab-${tabId}`).classList.add('active');
                    });
                });
            });
        }
    });
</script>

<?php include __DIR__ . '/auth/login.php'; ?>
<?php include __DIR__ . '/auth/register.php'; ?>

<style>
    /* Your existing styles remain unchanged */
    .tst-main-container {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .tst-category-tabs {
        margin-bottom: 2rem;
    }

    .tst-tab-header {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
        scrollbar-width: none;
    }

    .tst-tab-header::-webkit-scrollbar {
        display: none;
    }

    .tst-tab-btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        background: #f5f7fa;
        border: none;
        color: #5a6b8c;
        font-weight: 500;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
        font-size: 0.9375rem;
    }

    .tst-tab-btn:hover {
        background: #e1e7f0;
        color: #3a4a6b;
    }

    .tst-tab-btn.active {
        background: #4a80f0;
        color: white;
    }

    .tst-tab-content {
        display: none;
    }

    .tst-tab-content.active {
        display: block;
    }

    .tst-grid-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.75rem;
        padding: 0.5rem 0;
    }

    .tst-program-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 2rem 1.75rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f0f0f0;
        display: flex;
        flex-direction: column;
    }

    .tst-program-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        border-color: #e0e0e0;
    }

    .tst-card-title {
        font-size: 1.25rem;
        color: #1a1a1a;
        margin: 0 0 1.75rem 0;
        font-weight: 600;
        line-height: 1.4;
    }

    .tst-card-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: auto;
    }

    .tst-btn {
        flex: 1;
        padding: 0.75rem;
        border-radius: 10px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        text-decoration: none;
        font-size: 0.9375rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .tst-btn-primary {
        background: #4a80f0;
        color: white;
        border: 1px solid transparent;
    }

    .tst-btn-primary:hover {
        background: #3a70e0;
        transform: translateY(-1px);
    }

    .tst-empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #fafcff;
        border-radius: 16px;
        border: 1px dashed #e0e8f5;
    }

    .tst-empty-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 1.5rem;
        color: #c0d0e8;
    }

    .tst-empty-icon svg {
        width: 100%;
        height: 100%;
    }

    .tst-empty-text {
        font-size: 1.125rem;
        color: #6a6a6a;
        margin: 0;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .tst-tab-header {
            gap: 0.25rem;
        }
        
        .tst-tab-btn {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .tst-grid-wrapper {
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
        
        .tst-program-card {
            padding: 1.75rem 1.5rem;
        }
        
        .tst-card-actions {
            flex-direction: column;
        }
    }

    /* Your other existing styles remain unchanged */
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
