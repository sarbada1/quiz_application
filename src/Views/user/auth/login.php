<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" data-modal="loginModal">&times;</span>
        <h2>Login</h2>
        <div id="loginError" class="error-message"></div>

        <form id="loginForm" method="POST" action="<?= $url('user/login') ?>">
            <label for="loginUsername">Full name:</label>
            <input type="text" id="loginUsername" name="username" placeholder="Enter your full name" required>
            <span id="usernameError" class="error-message"></span>

            <label for="loginPassword">Password:</label>
            <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
            <span id="passwordError" class="error-message"></span>

            <button type="submit" class="primary">Login</button>
        </form>
        <p>Not registered? <a href="#" id="registerNowLink" class="text-info text-none">Register now</a></p>
    </div>
</div>
<script>
    function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(error => {
        error.textContent = '';
        error.style.display = 'none';
    });
}

function validateLoginForm(username, password) {
    const errors = {};
    
    if (!username || username.trim().length === 0) {
        errors.username = 'Username is required';
    }
    
    if (!password || password.length === 0) {
        errors.password = 'Password is required';
    }

    return errors;
}

document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors();

    const username = document.getElementById('loginUsername').value;
    const password = document.getElementById('loginPassword').value;
    const submitButton = e.target.querySelector('button[type="submit"]');

    // Validate form
    const errors = validateLoginForm(username, password);
    if (Object.keys(errors).length > 0) {
        Object.entries(errors).forEach(([field, message]) => {
            showError(`${field}Error`, message);
        });
        return;
    }

    submitButton.disabled = true;

    try {
        const formData = new FormData(this);
        const response = await fetch('<?= $url('user/login') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || 'Login failed');
        }

        if (result.success) {
            window.location.href = result.redirect || '/';
        } else {
            throw new Error(result.error || 'Invalid credentials');
        }

    } catch (error) {
        showError('loginError', error.message);
    } finally {
        submitButton.disabled = false;
    }
});
</script>