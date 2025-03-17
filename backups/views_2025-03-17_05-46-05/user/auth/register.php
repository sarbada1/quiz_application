<div id="registerModal" class="modal">
    <div class="modal-content">
        <span class="close" data-modal="registerModal">&times;</span>
        <h2>Register</h2>
        <form id="registerForm" method="POST" action="/user/register" novalidate>
            <div class="error-message" id="registerError"></div>

            <label for="regUsername">Full name:</label>
            <input type="text" id="regUsername" name="username" placeholder="Enter your full name" required minlength="3" pattern="[A-Za-z\s]+" autocomplete="name">
            <span class="error-message" id="usernameError"></span>

            <label for="regEmail">Email:</label>
            <input type="email" id="regEmail" name="email" placeholder="Enter your email address" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" autocomplete="email">
            <span class="error-message" id="emailError"></span>

            <label for="phone">Phone:</label>
            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required pattern="[0-9]{10}" autocomplete="tel">
            <small class="helper-text">Enter a valid 10-digit phone number. OTP will be sent to this number.</small>
            <span class="error-message" id="phoneError"></span>

            <label for="regPassword">Password:</label>
            <input type="password" id="regPassword" name="password" placeholder="Create a strong password" required minlength="8" autocomplete="new-password">
            <span class="error-message" id="passwordError"></span>

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="cpassword" placeholder="Re-enter your password" required minlength="8" autocomplete="new-password">
            <span class="error-message" id="confirmPasswordError"></span>


            <div class="g-recaptcha" data-sitekey="6LfRC48qAAAAAIOFJc8FnqMQPSGEjw-fWFSRI4Jf"></div>

            <button type="submit" class="primary">Register</button>
        </form>
        <p>Already registered? <a href="#" id="loginNowLink" class="text-info text-none">Login now</a></p>
    </div>
</div>
<!-- Add this after the register modal -->
<div id="otpModal" class="modal">
    <div class="modal-content">
        <h2>Verify Your Phone Number</h2>
        <p>We've sent a verification code to your phone number. Please enter it below.</p>

        <form id="otpForm" method="POST" action="/user/verify-otp" novalidate>
            <div class="otp-container">
                <input type="text" maxlength="1" class="otp-input" required>
                <input type="text" maxlength="1" class="otp-input" required>
                <input type="text" maxlength="1" class="otp-input" required>
                <input type="text" maxlength="1" class="otp-input" required>
                <input type="text" maxlength="1" class="otp-input" required>
                <input type="text" maxlength="1" class="otp-input" required>
            </div>
            <span class="error-message" id="otpError"></span>
            <button type="submit" class="primary">Verify</button>
        </form>
        <p class="otp-helper">Didn't receive the code? <a href="#" id="resendOtp">Resend</a></p>
    </div>
</div>

<style>
    .otp-container {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin: 20px 0;
    }

    .otp-input {
        width: 40px;
        height: 40px;
        text-align: center;
        font-size: 20px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .otp-input:focus {
        border-color: #007bff;
        outline: none;
    }

    .otp-helper {
        text-align: center;
        margin-top: 15px;
        font-size: 14px;
    }

    .error-message {
        color: #dc3545;
        font-size: 14px;
        margin: 5px 0;
        display: none;
    }

    .modal button[type="submit"] {
        position: relative;
    }

    .modal button[type="submit"]:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
</style>

<script>
    document.getElementById('registerForm').addEventListener('submit', async function(event) {
        event.preventDefault();
        clearErrors();

        const submitButton = event.target.querySelector('button[type="submit"]');
        if (!submitButton) return;

        try {
            submitButton.disabled = true;
            const formData = new FormData(this);

            // Validate form
            const validationErrors = validateRegistrationForm(formData);
            if (Object.keys(validationErrors).length > 0) {
                Object.entries(validationErrors).forEach(([field, message]) => {
                    showError(`${field}Error`, message);
                });
                throw new Error('Please correct the errors in the form');
            }

            const response = await fetch('/user/register', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || 'Registration failed');
            }

            if (result.success) {
                // Show OTP modal
                document.getElementById('registerModal').style.display = 'none';
                const otpModal = document.getElementById('otpModal');
                if (otpModal) {
                    otpModal.style.display = 'block';
                    const firstOtpInput = otpModal.querySelector('.otp-input');
                    if (firstOtpInput) firstOtpInput.focus();
                }
            } else {
                throw new Error(result.error || 'Registration failed');
            }
        } catch (error) {
            console.error('Registration error:', error);
            showError('registerError', error.message);
        } finally {
            submitButton.disabled = false;
        }
    });

    document.getElementById('otpForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const submitButton = e.target.querySelector('button[type="submit"]');
        if (submitButton) submitButton.disabled = true;

        try {
            const otp = Array.from(document.querySelectorAll('.otp-input'))
                .map(input => input.value)
                .join('');

            if (!otp || otp.length !== 6) {
                throw new Error('Please enter a valid 6-digit OTP');
            }

            const response = await fetch('/user/verify-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    otp
                })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || 'OTP verification failed');
            }

            if (result.success) {
                document.getElementById('otpModal').style.display = 'none';
                alert('Registration successful! Please login to continue.');
                window.location.href = '/';
            } else {
                throw new Error(result.error || 'Invalid OTP');
            }
        } catch (error) {
            console.error('OTP verification error:', error);
            showError('otpError', error.message);
        } finally {
            if (submitButton) submitButton.disabled = false;
        }
    });


    // OTP input handling
    document.querySelectorAll('.otp-input').forEach((input, index) => {
        input.addEventListener('keyup', (e) => {
            if (e.key >= 0 && e.key <= 9) {
                if (index < 5) {
                    document.querySelectorAll('.otp-input')[index + 1].focus();
                }
            } else if (e.key === 'Backspace') {
                if (index > 0) {
                    document.querySelectorAll('.otp-input')[index - 1].focus();
                }
            }
        });
    });

    // Resend OTP handler
    document.getElementById('resendOtp').addEventListener('click', async function(e) {
        e.preventDefault();
        if (this.disabled) return;

        try {
            const response = await fetch('/user/resend-otp', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            if (result.success) {
                this.disabled = true;
                startOtpTimer();
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            showError('otpError', error.message);
        }
    });

    function startOtpTimer(duration = 60) {
        const timerDisplay = document.createElement('span');
        timerDisplay.id = 'otpTimer';
        document.getElementById('resendOtp').parentNode.appendChild(timerDisplay);

        let timer = duration;
        const countdown = setInterval(() => {
            timerDisplay.textContent = ` (${timer}s)`;
            if (--timer < 0) {
                clearInterval(countdown);
                timerDisplay.remove();
                document.getElementById('resendOtp').disabled = false;
            }
        }, 1000);
    }

    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            errorElement.classList.add('error-message');
            // Scroll to error message
            errorElement.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }
    }

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(error => {
            error.textContent = '';
            error.style.display = 'none';
        });
    }

    function validateRegistrationForm(formData) {
        const errors = {};

        // Username validation
        const username = formData.get('username');
        if (!username || username.length < 3) {
            errors.username = 'Username must be at least 3 characters long';
        } else if (!/^[A-Za-z\s]+$/.test(username)) {
            errors.username = 'Username can only contain letters and spaces';
        }

        // Email validation
        const email = formData.get('email');
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.email = 'Please enter a valid email address';
        }

        // Phone validation
        const phone = formData.get('phone');
        if (!phone || !/^[0-9]{10}$/.test(phone)) {
            errors.phone = 'Please enter a valid 10-digit phone number';
        }

        // Password validation
        const password = formData.get('password');
        const cpassword = formData.get('cpassword');

        if (!password || password.length < 8) {
            errors.password = 'Password must be at least 8 characters long';
        }

        if (password !== cpassword) {
            errors.cpassword = 'Passwords do not match';
        }

        return errors;
    }
</script>