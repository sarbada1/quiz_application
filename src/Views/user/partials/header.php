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
                        <span>Mock Test</span>
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
                <li class="dropdown-parent" style='list-style-type: none;'>
                    <a href="#" class="user-profile-link" style="text-decoration:none;" onclick="return false;">
                        <div class="user-avatar-container">
                            <div class="user-avatar">
                                <?php if (!empty($_SESSION['profile_picture'])): ?>
                                    <img src="<?= htmlspecialchars($_SESSION['profile_picture']) ?>" alt="Profile Picture" class="avatar-image">
                                <?php else: ?>
                                    <div class="avatar-initials"><?= substr($_SESSION['name'], 0, 1) ?></div>
                                <?php endif; ?>
                                <?php if ($unreadReportsCount > 0): ?>
                                    <span class="notification-badge"><?= $unreadReportsCount > 9 ? '9+' : $unreadReportsCount ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="username-truncate"><?= htmlspecialchars(explode(' ', $_SESSION['name'])[0]) ?></span>
                            <i class="fas fa-caret-down dropdown-arrow"></i>
                        </div>
                    </a>
                    <ul class="dropdown-menu user-dropdown">
                        <li><a href="<?= $url('profile') ?>" class="dropdown-item"><i class="fas fa-user icon"></i> <span>Profile</span></a></li>
                        <li><a href="<?= $url('student/dashboard') ?>" class="dropdown-item"><i class="fas fa-tachometer-alt icon"></i> <span>Dashboard</span></a></li>
                        <?php if ($unreadReportsCount > 0): ?>
                            <li><a href="<?= $url('reports') ?>" class="dropdown-item notification-item">
                                <i class="fas fa-flag icon"></i> 
                                <span>Reports</span>
                                <span class="notification-count"><?= $unreadReportsCount > 9 ? '9+' : $unreadReportsCount ?></span>
                            </a></li>
                        <?php endif; ?>
                        <li class="dropdown-divider"></li>
                        <li><a href="<?= $url('user/logout') ?>" class="dropdown-item logout-item"><i class="fas fa-sign-out-alt icon"></i> <span>Logout</span></a></li>
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
</header>

<script>
    // Handle scroll effect
    window.addEventListener('scroll', function() {
        var navbar = document.getElementById('navbar');
        navbar.classList.toggle('navbar-scrolled', window.scrollY > 50);
    });

    // Mobile menu toggle
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const sidebarNav = document.querySelector('.sidebar-nav');
    
    mobileToggle.addEventListener('click', function() {
        sidebarNav.classList.toggle('active');
        this.querySelector('i').classList.toggle('fa-times');
    });

    // Handle dropdown behavior based on screen size
    function setupDropdowns() {
        const testLink = document.querySelector('.test-link');
        const testDropdown = document.querySelector('.test-dropdown');
        const userProfileLink = document.querySelector('.user-profile-link');
        const userDropdown = document.querySelector('.user-dropdown');
        
        if (window.innerWidth > 768) {
            // Desktop - hover behavior for test dropdown
            if (testLink) {
                testLink.addEventListener('mouseenter', showDropdown.bind(testLink, testDropdown));
                testDropdown.addEventListener('mouseleave', hideDropdown.bind(testDropdown));
                testLink.removeEventListener('click', toggleDropdown);
            }
            
            // Desktop - click behavior for user dropdown
            if (userProfileLink) {
                userProfileLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleDropdown.call(this, userDropdown);
                });
                userProfileLink.removeEventListener('mouseenter', showDropdown);
                userDropdown.removeEventListener('mouseleave', hideDropdown);
            }
        } else {
            // Mobile - click behavior for both dropdowns
            if (testLink) {
                testLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleDropdown.call(this, testDropdown);
                });
                testLink.removeEventListener('mouseenter', showDropdown);
                testDropdown.removeEventListener('mouseleave', hideDropdown);
            }
            
            if (userProfileLink) {
                userProfileLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleDropdown.call(this, userDropdown);
                });
            }
        }
    }

    function showDropdown(dropdown) {
        // Close other open dropdowns first
        document.querySelectorAll('.dropdown-menu').forEach(d => {
            if (d !== dropdown && d.style.display === 'block') {
                hideDropdown.call(d);
            }
        });
        
        dropdown.style.display = 'block';
        setTimeout(() => {
            dropdown.style.opacity = '1';
            dropdown.style.visibility = 'visible';
            dropdown.classList.add('active');
        }, 10);
    }

    function hideDropdown() {
        this.style.opacity = '0';
        this.style.visibility = 'hidden';
        this.classList.remove('active');
        setTimeout(() => {
            this.style.display = 'none';
        }, 300);
    }

    function toggleDropdown(dropdown, e) {
        if (dropdown.style.display === 'block') {
            hideDropdown.call(dropdown);
        } else {
            showDropdown.call(this, dropdown);
        }
    }

    // Initialize dropdowns and update on resize
    setupDropdowns();
    window.addEventListener('resize', setupDropdowns);

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-parent')) {
            document.querySelectorAll('.dropdown-menu').forEach(dropdown => {
                if (dropdown.style.display === 'block') {
                    hideDropdown.call(dropdown);
                }
            });
        }
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

    /* Logo */
    .logo h1 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        background: linear-gradient(135deg, #3498db, #2c3e50);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    /* Mobile Toggle */
    .mobile-menu-toggle {
        display: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #2c3e50;
    }

    /* Navigation Links */
    .sidebar-nav {
        display: flex;
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
        padding: 0.5rem 0;
        display: flex;
        align-items: center;
        transition: color 0.3s ease;
    }

    .nav-link:hover {
        color: #3498db;
    }

    /* Dropdown Menu */
    .dropdown-menu {
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

    .dropdown-item {
        display: flex;
        align-items: center;
        padding: 0.7rem 1.5rem;
        color: #2c3e50;
        text-decoration: none;
        transition: all 0.2s ease;
        position: relative;
    }

    .dropdown-item:hover {
        background: #f8f9fa;
        color: #3498db;
    }

    .dropdown-item .icon {
        margin-right: 12px;
        width: 18px;
        text-align: center;
        color: #7f8c8d;
    }

    .dropdown-item:hover .icon {
        color: #3498db;
    }

    /* User Dropdown Specific Styles */
    .user-dropdown {
        right: 0;
        left: auto;
    }

    .dropdown-divider {
        height: 1px;
        background: #ecf0f1;
        margin: 0.5rem 0;
    }

    .notification-item {
        position: relative;
    }

    .notification-count {
        position: absolute;
        right: 1.5rem;
        background: #e74c3c;
        color: white;
        border-radius: 50px;
        padding: 0.1rem 0.5rem;
        font-size: 0.7rem;
        font-weight: bold;
    }

    .logout-item {
        color: #e74c3c !important;
    }

    .logout-item:hover {
        background: #fde8e8 !important;
    }

    /* Enhanced User Avatar Styles */
    .user-avatar-container {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 5px 10px;
        border-radius: 50px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .user-avatar-container:hover {
        background: rgba(52, 152, 219, 0.1);
    }

    .user-avatar {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3498db, #2c3e50);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 16px;
    }

    .avatar-image {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .avatar-initials {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: linear-gradient(135deg, #3498db, #2c3e50);
        color: white;
        font-weight: bold;
    }

    .username-truncate {
        max-width: 100px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 600;
        color: #2c3e50;
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #e74c3c;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        border: 2px solid white;
    }

    /* Auth Buttons */
    .auth-buttons {
        display: flex;
        gap: 1rem;
    }

    .btn {
        padding: 0.7rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2980b9, #3498db);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(41, 128, 185, 0.3);
    }

    .btn-outline {
        background: transparent;
        border: 2px solid #3498db;
        color: #3498db;
    }

    .btn-outline:hover {
        background: rgba(52, 152, 219, 0.1);
    }

    /* Responsive Styles */
    @media (max-width: 992px) {
        #navbar {
            padding: 1rem;
        }
    }

    @media (max-width: 768px) {
        .mobile-menu-toggle {
            display: block;
        }

        .sidebar-nav {
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            background: white;
            flex-direction: column;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar-nav.active {
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

        .dropdown-menu {
            position: static;
            box-shadow: none;
            background: #f8f9fa;
            border-radius: 0;
            display: none;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            width: 100%;
        }

        .dropdown-menu.active {
            display: block;
            max-height: 500px;
            opacity: 1;
            visibility: visible;
        }

        .auth-buttons {
            margin-left: auto;
        }

        /* Mobile user avatar adjustments */
        .username-truncate {
            display: none;
        }
        
        .user-avatar-container {
            padding: 5px;
        }
    }

    @media (max-width: 480px) {
        .logo h1 {
            font-size: 1.5rem;
        }

        .btn {
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
        }
    }
</style>
