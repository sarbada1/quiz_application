<!-- Main content container -->
<div class="tst-main-container">
    <!-- Category Tabs -->
    <div class="tst-category-tabs">
        <div class="tst-tab-header">
            <button class="tst-tab-btn active" data-tab="all">All Programs</button>
            <?php 
            // Assuming $categories is an array of your program categories
            $categories = ['BCA', 'CSIT', 'Engineering', 'Medical']; // Example categories
            foreach ($categories as $category) : ?>
                <button class="tst-tab-btn" data-tab="<?php echo strtolower($category); ?>">
                    <?php echo $category; ?>
                </button>
            <?php endforeach; ?>
        </div>
        
        <div class="tst-tab-content active" id="tab-all">
            <?php if (!empty($programs)) : ?>
                <div class="tst-grid-wrapper">
                    <?php foreach ($programs as $program) : ?>
                        <div class="tst-program-card" data-category="<?php echo strtolower($program['category'] ?? 'all'); ?>">
                            <h3 class="tst-card-title"><?php echo htmlspecialchars($program['title']); ?></h3>
                            <div class="tst-card-actions">
                                <a href="<?= $url('test/' . $program['slug']) ?>" class="tst-btn tst-btn-primary">Mock Test</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="tst-empty-state">
                    <div class="tst-empty-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                    </div>
                    <p class="tst-empty-text">No test programs available at the moment</p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php foreach ($categories as $category) : ?>
            <div class="tst-tab-content" id="tab-<?php echo strtolower($category); ?>">
                <div class="tst-grid-wrapper">
                    <?php 
                    // Filter programs by category
                    $filteredPrograms = array_filter($programs, function($program) use ($category) {
                        return (strtolower($program['category'] ?? '') === strtolower($category));
                    });
                    
                    if (!empty($filteredPrograms)) : ?>
                        <?php foreach ($filteredPrograms as $program) : ?>
                            <div class="tst-program-card">
                                <h3 class="tst-card-title"><?php echo htmlspecialchars($program['title']); ?></h3>
                                <div class="tst-card-actions">
                                    <a href="<?= $url('test/' . $program['slug']) ?>" class="tst-btn tst-btn-primary">Mock Test</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="tst-empty-state">
                            <div class="tst-empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                </svg>
                            </div>
                            <p class="tst-empty-text">No <?php echo $category; ?> programs available</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/auth/login.php'; ?>
<?php include __DIR__ . '/auth/register.php'; ?>

<style>
    /* Base Container */
    .tst-main-container {
        max-width: 1200px;
        margin: 9rem auto;
        padding: 0 1.5rem;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Category Tabs */
    .tst-category-tabs {
        margin-bottom: 2rem;
    }

    .tst-tab-header {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
        scrollbar-width: none; /* Firefox */
    }

    .tst-tab-header::-webkit-scrollbar {
        display: none; /* Chrome/Safari */
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

    /* Grid Layout */
    .tst-grid-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.75rem;
        padding: 0.5rem 0;
    }

    /* Program Card */
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

    /* Card Title */
    .tst-card-title {
        font-size: 1.25rem;
        color: #1a1a1a;
        margin: 0 0 1.75rem 0;
        font-weight: 600;
        line-height: 1.4;
    }

    /* Card Actions */
    .tst-card-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: auto;
    }

    /* Buttons */
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

    /* Empty State */
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

    /* Responsive */
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
</style>

<script>
    // Tab switching functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tst-tab-btn');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons and content
                document.querySelectorAll('.tst-tab-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                document.querySelectorAll('.tst-tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Show corresponding content
                const tabId = this.getAttribute('data-tab');
                document.getElementById(`tab-${tabId}`).classList.add('active');
            });
        });
    });
</script>
