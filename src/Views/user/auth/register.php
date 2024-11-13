<div id="registerModal" class="modal">
    <div class="modal-content">
        <span class="close" data-modal="registerModal">&times;</span>
        <h2>Register</h2>
        <form id="registerForm" method="POST" action="/user/register">
            <label for="regUsername">Full name:</label>
            <input type="text" id="regUsername" name="username" required>

            <label for="regEmail">Email:</label>
            <input type="email" id="regEmail" name="email" required>

            <label for="regPassword">Password:</label>
            <input type="password" id="regPassword" name="password" required>

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="cpassword" required>

            <button type="submit" class="primary">Register</button>
        </form>
        <p>Already registered? <a href="#" id="loginNowLink" class="text-info text-none">Login now</a></p>
    </div>
</div>