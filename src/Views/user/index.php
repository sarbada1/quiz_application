<?php
session_start();
?>
<main>
    <section class="hero">
        <?php if (isset($_SESSION['name'])) { ?>
            <div class="user-info">
                <span class="ml-5 mt-5">Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</span>
                <div>

                    <button class="danger mt-5 mr-4"><a href="/user/logout" >Sign Out</a></button> 
                </div>
            </div>
        <?php }   ?>
        
        <h1>Welcome to QuizMaster</h1>
        <p>Test your knowledge, challenge your friends, and climb the leaderboard!</p>
        <?php if (!isset($_SESSION['name'])) { ?>
        <a href="#" class="cta-button" id="startQuizzingBtn">Start Quizzing Now</a>
        <?php }   ?>

    </section>

    <?php if (isset($_SESSION['message'])): ?>
        <div id="alert" class="alert mt-4 alert-<?= $_SESSION['status'] ?>" role="alert">
            <button type="button" class="closealert" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['status']); ?>
    <?php endif; ?>

    <section class="categories">
        <div class="container">
            <h2>Choose a Category</h2>
            <ul>
                <?php foreach ($categories as $category) { ?>
                    <li class="category-item">
                        <a href="/category/<?= $category['slug'] ?> ">
                            <h3><?= $category['name'] ?></h3>
                            <p>Explore quizzes in this category</p>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </section>
</main>

<!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Login</h2>
        <form id="loginForm" method="POST" action="/user/login">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <p>Not registered? <a href="#" id="registerNowLink">Register now</a></p>
    </div>
</div>

<!-- Register Modal -->
<div id="registerModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Register</h2>
        <form id="registerForm" method="POST" action="/user/register">
            <label for="regUsername">Username:</label>
            <input type="text" id="regUsername" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="regPassword">Password:</label>
            <input type="password" id="regPassword" name="password" required>

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="cpassword" required>

            <button type="submit">Register</button>
        </form>
        <p>Already registered? <a href="#" id="loginNowLink">Login now</a></p>
    </div>
</div>
