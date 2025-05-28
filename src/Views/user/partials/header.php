<?php
$unreadReportsCount = $_SESSION['unreadReportsCount'] ?? 0;
?>

<header id="navbar-container">
    <div id="navbar">
        <div class="logo">
            <a href="/" class="text-none">
                <h1>QuizPortal</h1>
            </a>
        </div>
        
        <!-- Mobile Menu Toggle -->
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-links">
                <li><a href="/" class="nav-link">Home</a></li>
             
                <li class="dropdown-parent">
                    <a href="<?= $url('test') ?>" class="nav-link test-link">
                        <span>Test</span>
                        <i class="fas fa-caret-down dropdown-arrow"></i>
                    </a>
                    <ul class="dropdown-menu test-dropdown">
                    <?php if (isset($programs) && is_array($programs)): ?>
                            <?php foreach ($programs as $index => $program):
                                if ($index <= 5) { ?>
                                    <li><a href="<?= $url('test/' . $program['slug']) ?>" class="dropdown-item"><?= htmlspecialchars($program['title']) ?></a></li>
                                <?php  }
                                ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><span class="dropdown-item">No test found</span></li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </nav>
        
        <?php if (isset($_SESSION['name'])) { ?>
            <div class="user-actions">
                <nav class="user-nav">
                    <ul>
                        <li class="dropdown-parent">
                            <a href="#" class="user-profile-link">
                                <div class="user-avatar">
                                    <i class="fas fa-user-circle"></i>
                                    <?php if ($unreadReportsCount > 0): ?>
                                        <span class="notification-badge"><?= $unreadReportsCount ?></span>
                                    <?php endif; ?>
                                </div>
                                <i class="fas fa-caret-down dropdown-arrow"></i>
                            </a>
                            <ul class="dropdown-menu user-dropdown">
                                <li><a href="<?= $url('profile') ?>" class="dropdown-item"><i class="fas fa-user"></i> Profile</a></li>
                                <li><a href="<?= $url('student/dashboard') ?>" class="dropdown-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <li><a href="<?= $url('user/logout') ?>" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php } else { ?>
            <div class="auth-buttons">
                <button class="btn btn-outline" id="startQuizzingBtn">Login</button>
                <button class="btn btn-primary" id="signUpLink">Sign Up</button>
            </div>
        <?php } ?>
    </div>
</header>

<script>
    // Existing scroll function
    window.addEventListener('scroll', function() {
        var navbar = document.getElementById('navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
    });

    // Mobile menu toggle functionality
    document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
        document.querySelector('.sidebar-nav').classList.toggle('mobile-menu-active');
        this.querySelector('i').classList.toggle('fa-times');
        this.querySelector('i').classList.toggle('fa-bars');
    });

    // For desktop hover behavior
    if (window.innerWidth > 768) {
        const testLink = document.querySelector('.test-link');
        const testDropdown = document.querySelector('.test-dropdown');
        
        testLink.addEventListener('mouseenter', () => {
            testDropdown.style.display = 'block';
            testDropdown.style.opacity = '1';
            testDropdown.style.visibility = 'visible';
        });
        
        // Hide when mouse leaves either the link or dropdown
        testLink.addEventListener('mouseleave', (e) => {
            // Check if mouse is moving to dropdown
            if (!e.relatedTarget || !e.relatedTarget.closest('.test-dropdown')) {
                testDropdown.style.display = 'none';
                testDropdown.style.opacity = '0';
                testDropdown.style.visibility = 'hidden';
            }
        });
        
        testDropdown.addEventListener('mouseleave', () => {
            testDropdown.style.display = 'none';
            testDropdown.style.opacity = '0';
            testDropdown.style.visibility = 'hidden';
        });
    }

    // For mobile click behavior
    if (window.innerWidth <= 768) {
        document.querySelector('.test-link').addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.nextElementSibling;
            if (dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
            } else {
                dropdown.style.display = 'block';
            }
        });
    }
</script>

<style>
    /* Base Styles */
    #navbar-container {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    #navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Logo, nav links, dropdown arrows styles remain the same as before */
    /* ... (keep all your existing styles) ... */

    /* Enhanced Test Dropdown Menu Styles */
    .test-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        list-style: none;
        padding: 0.5rem 0;
        margin: 0;
        min-width: 250px;
        opacity: 0;
        visibility: hidden;
        display: none;
        transition: all 0.3s ease;
        z-index: 100;
    }

    /* Keep all your other existing styles */
    /* ... (rest of your CSS) ... */

    @media (max-width: 768px) {
        /* Mobile styles remain the same */
        .test-dropdown {
            position: static;
            box-shadow: none;
            background: #f8f9fa;
            border-radius: 0;
            display: none;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .test-dropdown.active {
            display: block;
            max-height: 500px;
            padding: 0.5rem 0;
        }
    }
</style>
