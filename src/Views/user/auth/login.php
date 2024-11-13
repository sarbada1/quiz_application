<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" data-modal="loginModal">&times;</span>
        <h2>Login</h2>
        <form id="loginForm" method="POST" action="/user/login">
            <label for="loginUsername">Full name:</label>
            <input type="text" id="loginUsername" name="username" required>

            <label for="loginPassword">Password:</label>
            <input type="password" id="loginPassword" name="password" required>

            <button type="submit" class="primary">Login</button>
        </form>
        <p>Not registered? <a href="#" id="registerNowLink" class="text-info text-none">Register now</a></p>
    </div>
</div>