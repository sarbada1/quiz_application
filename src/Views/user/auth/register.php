    <div id="registerModal" class="modal">
    <div class="modal-content">
        <span class="close" data-modal="registerModal">&times;</span>
        <h2>Register</h2>
        <form id="registerForm" method="POST" action="<?= $url('user/register') ?>" novalidate>
            <div class="error-message" id="registerError"></div>

            <label for="regUsername">Full name:</label>
            <input type="text" id="regUsername" name="username" placeholder="Enter your full name" required minlength="3" pattern="[A-Za-z\s]+" autocomplete="name">
            <span class="error-message" id="usernameError"></span>

            <label for="regEmail">Email:</label>
            <input type="email" id="regEmail" name="email" placeholder="Enter your email address" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" autocomplete="email">
            <span class="error-message" id="emailError"></span>

            <label for="phone">Phone:</label>
            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required pattern="[0-9]{10}" autocomplete="tel">
            <small class="helper-text">Enter a valid 10-digit phone number.</small>
            <span class="error-message" id="phoneError"></span>

            <label for="regPassword">Password:</label>
            <input type="password" id="regPassword" name="password" placeholder="Create a strong password" required minlength="8" autocomplete="new-password">
            <span class="error-message" id="passwordError"></span>

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="cpassword" placeholder="Re-enter your password" required minlength="8" autocomplete="new-password">
            <span class="error-message" id="confirmPasswordError"></span>

            <div class="g-recaptcha" data-sitekey="6LfRC48qAAAAAIOFJc8FnqMQPSGEjw-fWFSRI4Jf"></div>

            <button type="submit" class="primary">
                <span class="spinner" style="display:none;"></span>
                Register
            </button>
        </form>
        <p>Already registered? <a href="#" id="loginNowLink" class="text-info text-none">Login now</a></p>
    </div>
</div>

<!-- OTP Verification Modal -->
<div id="otpModal" class="modal">
    <div class="modal-content">
        <span class="close" data-modal="otpModal">&times;</span>
        <h2>Verify Your Email</h2>
        <div class="verification-icon">
            <i class="fas fa-envelope-circle-check"></i>
        </div>
        <p>We've sent a verification code to your email address. Please enter it below to complete your registration.</p>
        
        <form id="otpForm" method="POST" action="<?= $url('user/verify-otp') ?>" novalidate>
            <div class="error-message" id="otpError"></div>
            
            <div class="otp-container">
                <input type="text" maxlength="1" class="otp-input" pattern="[0-9]" required>
                <input type="text" maxlength="1" class="otp-input" pattern="[0-9]" required>
                <input type="text" maxlength="1" class="otp-input" pattern="[0-9]" required>
                <input type="text" maxlength="1" class="otp-input" pattern="[0-9]" required>
                <input type="text" maxlength="1" class="otp-input" pattern="[0-9]" required>
                <input type="text" maxlength="1" class="otp-input" pattern="[0-9]" required>
            </div>
            
            <p>Didn't receive the code?</p>
            <p class="resend-container">
                <a href="#" id="resendOtp" class="resend-link">Resend code</a>
                <span id="timer" style="display: none;"></span>
            </p>
            
            <button type="submit" class="primary">
                <span class="spinner" style="display:none;"></span>
                Verify and Complete Registration
            </button>
        </form>
    </div>
</div>

<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        overflow: auto;
    }

    .modal-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 25px;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        position: relative;
        animation: modalFade 0.3s;
    }

    @keyframes modalFade {
        from {opacity: 0; transform: translateY(-20px);}
        to {opacity: 1; transform: translateY(0);}
    }

    .close {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
        color: #777;
    }
    
    .close:hover {
        color: #333;
    }
    
    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }
    
    form label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    form input[type="text"],
    form input[type="email"],
    form input[type="tel"],
    form input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }
    
    form input:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }
    
    button.primary {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 12px 20px;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
        margin-top: 10px;
        position: relative;
    }
    
    button.primary:hover {
        background-color: #0069d9;
    }
    
    button.primary:disabled {
        background-color: #80b0e6;
        cursor: not-allowed;
    }
    
    .error-message {
        color: #dc3545;
        font-size: 14px;
        margin-bottom: 15px;
        display: none;
        padding: 8px;
        border-radius: 4px;
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .helper-text {
        color: #6c757d;
        font-size: 12px;
        margin-top: -10px;
        margin-bottom: 10px;
        display: block;
    }
    
    /* OTP Styles */
    .otp-container {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin: 25px 0;
    }
    
    .otp-input {
        width: 45px;
        height: 50px;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        border: 2px solid #ddd;
        border-radius: 4px;
    }
    
    .otp-input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }
    
    .verification-icon {
        text-align: center;
        font-size: 64px;
        margin: 15px 0;
        color: #007bff;
    }
    
    .resend-container {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .resend-link {
        color: #007bff;
        text-decoration: none;
    }
    
    .resend-link:hover {
        text-decoration: underline;
    }
    
    .resend-link.disabled {
        color: #6c757d;
        pointer-events: none;
    }
    
    #timer {
        font-size: 14px;
        color: #6c757d;
        margin-left: 5px;
    }
    
    /* Spinner */
    .spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
        margin-right: 10px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<script>
    // Registration form submission handler
    document.getElementById('registerForm').addEventListener('submit', async function(event) {
        event.preventDefault();
        clearErrors();
        
        const submitButton = this.querySelector('button[type="submit"]');
        const spinner = submitButton.querySelector('.spinner');
        
        try {
            // Disable button and show spinner
            submitButton.disabled = true;
            spinner.style.display = 'inline-block';
            
            // Validate form data
            const formData = new FormData(this);
            const validationErrors = validateRegistrationForm(formData);
            
            if (Object.keys(validationErrors).length > 0) {
                Object.entries(validationErrors).forEach(([field, message]) => {
                    showError(`${field}Error`, message);
                });
                throw new Error('Please correct the errors in the form');
            }
            
            // Send form data to server
            const response = await fetch('<?= $url('user/register') ?>', {
                method: 'POST',
                body: formData
            });
            
            // Parse response
            const result = await response.json();
            console.log('Registration result:', result);
            
            if (!result.success) {
                throw new Error(result.error || 'Registration failed');
            }
            
            // Show OTP verification modal
            document.getElementById('registerModal').style.display = 'none';
            document.getElementById('otpModal').style.display = 'block';
            
            // Focus on first OTP input
            document.querySelector('.otp-input').focus();
            
            // Start OTP timer
            startOtpTimer();
            
        } catch (error) {
            console.error('Registration error:', error);
            showError('registerError', error.message);
        } finally {
            // Re-enable button and hide spinner
            submitButton.disabled = false;
            spinner.style.display = 'none';
        }
    });
    
    // OTP form submission handler
    document.getElementById('otpForm').addEventListener('submit', async function(event) {
        event.preventDefault();
        clearErrors();
        
        const submitButton = this.querySelector('button[type="submit"]');
        const spinner = submitButton.querySelector('.spinner');
        
        try {
            // Disable button and show spinner
            submitButton.disabled = true;
            spinner.style.display = 'inline-block';
            
            // Get OTP from inputs
            const otpInputs = document.querySelectorAll('.otp-input');
            const otpArray = Array.from(otpInputs).map(input => input.value);
            const otp = otpArray.join('');
            
            // Validate OTP format
            if (!/^\d{6}$/.test(otp)) {
                throw new Error('Please enter a valid 6-digit verification code');
            }
            
            // Send OTP to server
            const response = await fetch('<?= $url('user/verify-otp') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ otp })
            });
            
            // Parse response
            const result = await response.json();
            console.log('OTP verification result:', result);
            
            if (!result.success) {
                throw new Error(result.error || 'Verification failed');
            }
            
            // Registration successful
            document.getElementById('otpModal').style.display = 'none';
            
            // Show success message
            alert('Registration successful! You are now logged in.');
            
            // Redirect to homepage
            window.location.href = '/';
            
        } catch (error) {
            console.error('OTP verification error:', error);
            showError('otpError', error.message);
        } finally {
            // Re-enable button and hide spinner
            submitButton.disabled = false;
            spinner.style.display = 'none';
        }
    });
    
    // OTP input handling
    document.querySelectorAll('.otp-input').forEach((input, index) => {
        // Only allow numbers
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Auto-advance to next input
            if (this.value && index < 5) {
                document.querySelectorAll('.otp-input')[index + 1].focus();
            }
        });
        
        // Handle backspace
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                document.querySelectorAll('.otp-input')[index - 1].focus();
            }
        });
        
        // Handle paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            
            if (/^\d+$/.test(paste)) {
                const digits = paste.split('');
                
                // Fill in all inputs if possible
                document.querySelectorAll('.otp-input').forEach((input, i) => {
                    if (digits[i]) {
                        input.value = digits[i];
                    }
                });
                
                // Focus on appropriate field
                const nextIndex = Math.min(index + paste.length, 5);
                document.querySelectorAll('.otp-input')[nextIndex].focus();
            }
        });
    });
    
    // Resend OTP handler
    document.getElementById('resendOtp').addEventListener('click', async function(e) {
        e.preventDefault();
        
        if (this.classList.contains('disabled')) {
            return;
        }
        
        try {
            this.classList.add('disabled');
            
            const response = await fetch('<?= $url('user/resend-otp') ?>', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Clear OTP fields
                document.querySelectorAll('.otp-input').forEach(input => {
                    input.value = '';
                });
                
                // Focus on first field
                document.querySelector('.otp-input').focus();
                
                // Reset timer
                startOtpTimer();
                
                alert('A new verification code has been sent to your email.');
            } else {
                throw new Error(result.error || 'Failed to resend verification code');
            }
        } catch (error) {
            console.error('Resend OTP error:', error);
            showError('otpError', error.message);
            this.classList.remove('disabled');
        }
    });
    
    // Close modal buttons
    document.querySelectorAll('.close').forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal');
            document.getElementById(modalId).style.display = 'none';
        });
    });
    
    // Switch to login
    document.getElementById('loginNowLink').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('registerModal').style.display = 'none';
        document.getElementById('loginModal').style.display = 'block';
    });
    
    function startOtpTimer(duration = 60) {
        const resendLink = document.getElementById('resendOtp');
        const timer = document.getElementById('timer');
        
        resendLink.classList.add('disabled');
        timer.style.display = 'inline-block';
        
        let seconds = duration;
        timer.textContent = `(${seconds}s)`;
        
        const interval = setInterval(() => {
            seconds--;
            
            timer.textContent = `(${seconds}s)`;
            
            if (seconds <= 0) {
                clearInterval(interval);
                resendLink.classList.remove('disabled');
                timer.style.display = 'none';
            }
        }, 1000);
    }
    
    function showError(elementId, message) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = message;
            element.style.display = 'block';
        }
    }
    
    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(element => {
            element.textContent = '';
            element.style.display = 'none';
        });
    }
    
    function validateRegistrationForm(formData) {
        const errors = {};
        
        // Username validation
        const username = formData.get('username');
        if (!username || username.length < 3) {
            errors.username = 'Name must be at least 3 characters long';
        } else if (!/^[A-Za-z\s]+$/.test(username)) {
            errors.username = 'Name can only contain letters and spaces';
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