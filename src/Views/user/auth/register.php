<div id="registerModal" class="modal">
    <div class="modal-content">
        <span class="close" data-modal="registerModal">&times;</span>
        <h2>Register</h2>
        <form id="registerForm" method="POST" action="/user/register" novalidate>
            <div class="error-message" id="registerError"></div>

            <label for="regUsername">Full name:</label>
            <input type="text" id="regUsername" name="username" required minlength="3" pattern="[A-Za-z\s]+" autocomplete="name">
            <span class="error-message" id="usernameError"></span>

            <label for="regEmail">Email:</label>
            <input type="email" id="regEmail" name="email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" autocomplete="email">
            <span class="error-message" id="emailError"></span>

            <label for="phone">Phone:</label>
            <input type="tel" id="phone" name="phone" required pattern="[0-9]{10}" autocomplete="tel">
            <span class="error-message" id="phoneError"></span>

            <label for="regPassword">Password:</label>
            <input type="password" id="regPassword" name="password" required minlength="8" autocomplete="new-password">
            <span class="error-message" id="passwordError"></span>

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="cpassword" required minlength="8" autocomplete="new-password">
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
    
    const submitButton = event.target.querySelector('button[type="submit"]');
    if (!submitButton) return;

    try {
        submitButton.disabled = true;
        const formData = new FormData(this);
        console.log(formData);
        
        const response = await fetch('/user/register', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();
console.log(result);

        if (result.success) {
            // Only show OTP modal on success
            document.getElementById('registerModal').style.display = 'none';
            const otpModal = document.getElementById('otpModal');
            if (!otpModal) {
                throw new Error('OTP modal not found');
            }
            otpModal.style.display = 'block';
            const firstOtpInput = otpModal.querySelector('.otp-input');
            if (firstOtpInput) {
                firstOtpInput.focus();
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
    
    try {
        // Get submit button and disable it
        const submitButton = e.target.querySelector('button[type="submit"]');
        if (submitButton) submitButton.disabled = true;

        // Collect OTP from inputs
        const otp = Array.from(document.querySelectorAll('.otp-input'))
            .map(input => input.value)
            .join('');
            
        // Send OTP verification request    
        const response = await fetch('/user/verify-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ otp })
        });

        // Clone response before reading
        const responseClone = response.clone();
        
        let result;
        try {
            result = await response.json();
        } catch (e) {
            console.error('Failed to parse JSON:', e);
            const text = await responseClone.text();
            console.error('Raw response:', text);
            throw new Error('Invalid server response');
        }


        if (result.success) {
            // Hide OTP modal
            document.getElementById('otpModal').style.display = 'none';
            // Show success message
            alert('Registration successful! Please login to continue.');
            window.location.href = '/login';
        } else {
            throw new Error(result.error || 'Invalid OTP');
        }

    } catch (error) {
        showError('otpError', error.message);
    } finally {
        // Re-enable submit button
        const submitButton = e.target.querySelector('button[type="submit"]');
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
            headers: { 'Accept': 'application/json' }
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
    }
    console.error(`Error: ${message}`);
}
</script>