<?php
$unreadReportsCount = $_SESSION['unreadReportsCount'] ?? 0;
// print_r($quizzes);   
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
                    <a href="<?= $url('test') ?>" class="nav-link">
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

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-parent')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('dropdown-active');
            });
        }
    });

    // Toggle dropdowns - specifically for the Test dropdown
    document.querySelectorAll('.dropdown-parent > a').forEach(link => {
        link.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const dropdown = this.nextElementSibling;
                dropdown.classList.toggle('dropdown-active');
            }
            
            // For desktop, ensure Test dropdown stays visible on hover
            if (this.parentElement.classList.contains('dropdown-parent') && 
                this.nextElementSibling.classList.contains('test-dropdown') &&
                window.innerWidth > 768) {
                e.preventDefault();
                this.nextElementSibling.classList.toggle('dropdown-active');
            }
        });
    });

    // Keep Test dropdown visible when hovering over it
    document.querySelectorAll('.test-dropdown').forEach(dropdown => {
        dropdown.addEventListener('mouseenter', function() {
            this.classList.add('dropdown-active');
        });
        
        dropdown.addEventListener('mouseleave', function() {
            if (window.innerWidth > 768) {
                this.classList.remove('dropdown-active');
            }
        });
    });
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

    #navbar.navbar-scrolled {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        background: rgba(255, 255, 255, 0.98);
    }

    /* Logo Styles */
    .logo h1 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #3498db;
        margin: 0;
        background: linear-gradient(135deg, #3498db, #2c3e50);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        transition: all 0.3s ease;
    }

    .logo a:hover h1 {
        opacity: 0.9;
    }

    /* Navigation Styles */
    .sidebar-nav {
        display: flex;
        transition: all 0.3s ease;
    }

    .nav-links {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        align-items: center;
    }

    .nav-links li {
        position: relative;
        margin: 0 1rem;
    }

    .nav-link {
        color: #2c3e50;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        padding: 0.5rem 0;
        position: relative;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        color: #3498db;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, #3498db, #9b59b6);
        transition: width 0.3s ease;
    }

    .nav-link:hover::after {
        width: 100%;
    }

    .dropdown-arrow {
        margin-left: 0.5rem;
        transition: transform 0.3s ease;
    }

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
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        transition: all 0.3s ease;
        z-index: 100;
        display: block !important; /* Force display */
    }

    .dropdown-parent:hover .test-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .test-dropdown.dropdown-active {
        display: block !important;
    }

    .dropdown-item {
        display: block;
        padding: 0.7rem 1.5rem;
        color: #2c3e50;
        text-decoration: none;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background: #f8f9fa;
        color: #3498db;
        padding-left: 1.7rem;
    }

    .dropdown-item i {
        margin-right: 0.7rem;
        width: 1rem;
        text-align: center;
    }

    /* User Profile Styles */
    .user-actions {
        display: flex;
        align-items: center;
    }

    .user-profile-link {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #2c3e50;
    }

    .user-avatar {
        position: relative;
        display: flex;
        align-items: center;
    }

    .user-avatar i {
        font-size: 2rem;
        color: #3498db;
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #e74c3c;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .user-dropdown {
        right: 0;
        left: auto;
    }

    /* Auth Button Styles */
    .auth-buttons {
        display: flex;
        gap: 1rem;
    }

    .btn {
        padding: 0.7rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
    }

    .btn-outline {
        background: transparent;
        border-color: #3498db;
        color: #3498db;
    }

    .btn-outline:hover {
        background: rgba(52, 152, 219, 0.1);
    }

    /* Mobile Menu Styles */
    .mobile-menu-toggle {
        display: none;
        font-size: 1.5rem;
        color: #2c3e50;
        cursor: pointer;
        padding: 0.5rem;
    }

    /* Responsive Styles */
    @media (max-width: 992px) {
        #navbar {
            padding: 1rem;
        }
        
        .nav-links li {
            margin: 0 0.7rem;
        }
    }

    @media (max-width: 768px) {
        .mobile-menu-toggle {
            display: block;
            order: 1;
        }
        
        .logo {
            order: 0;
            flex-grow: 1;
            text-align: center;
        }
        
        .auth-buttons, .user-actions {
            order: 2;
        }
        
        .sidebar-nav {
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            background: white;
            flex-direction: column;
            align-items: stretch;
            max-height: 0;
            overflow: hidden;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-nav.mobile-menu-active {
            max-height: 500px;
            padding: 1rem 0;
        }
        
        .nav-links {
            flex-direction: column;
            align-items: stretch;
        }
        
        .nav-links li {
            margin: 0;
        }
        
        .nav-link {
            padding: 1rem 2rem;
        }
        
        .test-dropdown {
            position: static;
            opacity: 1;
            visibility: visible;
            transform: none;
            box-shadow: none;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: #f8f9fa;
            border-radius: 0;
            padding: 0;
            display: none !important;
        }
        
        .test-dropdown.dropdown-active {
            max-height: 500px;
            padding: 0.5rem 0;
            display: block !important;
        }
        
        .dropdown-parent:hover .test-dropdown {
            transform: none;
        }
        
        .dropdown-item {
            padding-left: 3rem;
        }
    }

    @media (max-width: 480px) {
        .auth-buttons {
            gap: 0.5rem;
        }
        
        .btn {
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
        }
        
        .logo h1 {
            font-size: 1.5rem;
        }
    }
</style>
