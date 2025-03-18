<?php
$unreadReportsCount = $_SESSION['unreadReportsCount'] ?? 0;
// print_r($quizzes);   
?>

<header id="navbar-container">
    <div id="navbar">
        <div class="logo ml-5">
            <a href="/" class="text-none ">
                <h1>QuizPortal</h1>
            </a>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="/">Home</a></li>
                <li>
                    <a href="<?= $url('quiz') ?>">
                        <span>Quiz</span>
                        <i class="fas fa-caret-down ml-3 arrow"></i>
                    </a>
                    <ul class="dropdown">
                        <?php if (isset($quizzes) && is_array($quizzes)): ?>
                            <?php foreach ($quizzes as $index => $category):
                                if ($index <= 5) { ?>
                                    <li><a href="<?= $url('quiz/' . $category['slug']) ?>"><?= htmlspecialchars($category['title']) ?></a></li>
                                <?php  }
                                ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No categories found</li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li>
                    <a href="<?= $url('test') ?>">
                        <span>Test</span>
                        <i class="fas fa-caret-down ml-3 arrow"></i>
                    </a>
                    <ul class="dropdown">
                    <?php if (isset($programs) && is_array($programs)): ?>
                            <?php foreach ($programs as $index => $program):
                                if ($index <= 5) { ?>
                                    <li><a href="<?= $url('test/' . $program['slug']) ?>"><?= htmlspecialchars($program['title']) ?></a></li>
                                <?php  }
                                ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No test found</li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </nav>
        <?php if (isset($_SESSION['name'])) { ?>
        
            <div class="user flex align-center">
                <nav class="sidebar-nav mr-5 ">
                    <ul>
                        <li>
                            <a href="#">
                                <i class="fas fa-user-circle" style="font-size: 35px;"></i>
                                <i class="fas fa-caret-down ml-3 arrow" style="font-size: 20px;"></i>
                            </a>
                            <ul class="dropdown mr-5   ">
                                <li><a href="<?= $url('profile') ?>"> Profile</a></li>
                                <li><a href="<?= $url('user/logout') ?>"> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php } else { ?>
            <div class="flex w-15 justify-between">
                <button class="primary text-lg" id="signUpLink">Sign Up</button>
                <button class="bg-transparent text-dark text-lg" id="startQuizzingBtn">Login</button>
            </div>
        <?php   } ?>
    </div>
</header>

<script>
    window.addEventListener('scroll', function() {
        var navbar = document.getElementById('navbar');

        if (window.scrollY > 50) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
    });
</script>
<style>
    .notification-icon {
        position: relative;
        display: inline-block;
    }

    .notification-icon .badge {
        position: absolute;
        top: -10px;
        right: -10px;
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 5px 10px;
        font-size: 10px;
    }
</style>