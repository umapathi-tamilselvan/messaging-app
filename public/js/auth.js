/* ===================================
   Authentication JavaScript
   =================================== */

(function() {
    'use strict';
    
    // API_BASE_URL is not needed since axios.defaults.baseURL is already set to '/api' in app.js
    // We'll use relative URLs that axios will automatically prepend with baseURL
    let phoneNumber = '';
    let resendTimer = null;
    let timerSeconds = 60;
    let isRegister = false;
    
    // Check URL parameter for action type
    const urlParams = new URLSearchParams(window.location.search);
    const action = urlParams.get('action');
    isRegister = action === 'register';
    
    // Update UI based on action
    const authTitle = document.getElementById('authTitle');
    const authSubtitle = document.getElementById('authSubtitle');
    const authIcon = document.getElementById('authIcon');
    const registerNotice = document.getElementById('registerNotice');
    
    if (isRegister) {
        if (authTitle) authTitle.textContent = 'Create Account';
        if (authSubtitle) authSubtitle.textContent = 'Enter your phone number to get started';
        if (authIcon) {
            authIcon.className = 'bi bi-person-plus-fill text-success fs-1';
        }
        if (registerNotice) registerNotice.classList.remove('d-none');
        
        // Update button text
        const sendOtpText = document.getElementById('sendOtpText');
        if (sendOtpText) sendOtpText.textContent = 'Continue';
        
        const verifyBtnText = document.getElementById('verifyBtnText');
        if (verifyBtnText) verifyBtnText.textContent = 'Create Account';
    } else {
        if (authTitle) authTitle.textContent = 'Welcome Back';
        if (authSubtitle) authSubtitle.textContent = 'Enter your phone number to receive an OTP';
        if (authIcon) {
            authIcon.className = 'bi bi-box-arrow-in-right text-primary fs-1';
        }
    }
    
    // DOM Elements
    const phoneStep = document.getElementById('phoneStep');
    const otpStep = document.getElementById('otpStep');
    const phoneForm = document.getElementById('phoneForm');
    const otpForm = document.getElementById('otpForm');
    const phoneInput = document.getElementById('phone');
    const otpInputs = document.querySelectorAll('.otp-input');
    const hiddenOtpInput = document.getElementById('otp');
    const phoneDisplay = document.getElementById('phoneDisplay');
    const changePhoneBtn = document.getElementById('changePhoneBtn');
    const resendTimerEl = document.getElementById('resendTimer');
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const phoneSpinner = document.getElementById('phoneSpinner');
    const otpSpinner = document.getElementById('otpSpinner');
    
    // Initialize OTP input handling
    function initOtpInputs() {
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                const value = e.target.value;
                if (value && /[0-9]/.test(value)) {
                    if (index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                    updateHiddenOtp();
                } else {
                    e.target.value = '';
                }
            });
            
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
            
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').trim();
                if (/^\d{6}$/.test(pastedData)) {
                    pastedData.split('').forEach((char, i) => {
                        if (otpInputs[i]) {
                            otpInputs[i].value = char;
                        }
                    });
                    updateHiddenOtp();
                    otpInputs[otpInputs.length - 1].focus();
                }
            });
        });
    }
    
    function updateHiddenOtp() {
        const otp = Array.from(otpInputs).map(input => input.value).join('');
        hiddenOtpInput.value = otp;
    }
    
    function resetOtpInputs() {
        otpInputs.forEach(input => input.value = '');
        hiddenOtpInput.value = '';
        otpInputs[0].focus();
    }
    
    function startResendTimer() {
        timerSeconds = 60;
        resendTimerEl.classList.remove('d-none');
        resendOtpBtn.classList.add('d-none');
        
        resendTimer = setInterval(() => {
            timerSeconds--;
            document.getElementById('timerCount').textContent = timerSeconds;
            
            if (timerSeconds <= 0) {
                clearInterval(resendTimer);
                resendTimerEl.classList.add('d-none');
                resendOtpBtn.classList.remove('d-none');
            }
        }, 1000);
    }
    
    function stopResendTimer() {
        if (resendTimer) {
            clearInterval(resendTimer);
            resendTimer = null;
        }
    }
    
    // Phone Form Submission
    phoneForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        phoneNumber = phoneInput.value.trim();
        
        if (!phoneNumber) {
            showError('phone', 'Please enter a phone number');
            return;
        }
        
        // Validate phone format (basic validation)
        const phoneRegex = /^\+?[1-9]\d{1,14}$/;
        if (!phoneRegex.test(phoneNumber)) {
            showError('phone', 'Please enter a valid phone number');
            return;
        }
        
        // Show loading state
        sendOtpBtn.disabled = true;
        phoneSpinner.classList.remove('d-none');
        
        try {
            const response = await axios.post('/auth/otp', {
                phone: phoneNumber
            });
            
            if (response.data.success) {
                // Switch to OTP step
                phoneStep.classList.add('d-none');
                otpStep.classList.remove('d-none');
                phoneDisplay.textContent = phoneNumber;
                resetOtpInputs();
                startResendTimer();
                
                // Show success message if OTP is shown in debug mode
                if (response.data.message && response.data.message.includes('OTP:')) {
                    window.utils.showNotification(`OTP: ${response.data.message.split('OTP:')[1].trim()}`, 'info');
                } else {
                    window.utils.showNotification('OTP sent successfully!', 'success');
                }
            }
        } catch (error) {
            const errorMessage = error.response?.data?.message || error.response?.data?.errors?.phone?.[0] || 'Failed to send OTP. Please try again.';
            showError('phone', errorMessage);
            window.utils.showNotification(errorMessage, 'error');
        } finally {
            sendOtpBtn.disabled = false;
            phoneSpinner.classList.add('d-none');
        }
    });
    
    // OTP Form Submission
    otpForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const otp = hiddenOtpInput.value;
        
        if (otp.length !== 6) {
            showError('otp', 'Please enter the complete 6-digit OTP');
            return;
        }
        
        // Show loading state
        verifyOtpBtn.disabled = true;
        otpSpinner.classList.remove('d-none');
        
        try {
            const response = await axios.post('/auth/verify', {
                phone: phoneNumber,
                otp: otp
            });
            
            if (response.data.token) {
                // Store token and user data
                localStorage.setItem('auth_token', response.data.token);
                localStorage.setItem('user', JSON.stringify(response.data.user));
                
                // Set authorization header
                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
                
                // Show appropriate success message
                const successMessage = isRegister ? 'Account created successfully! Welcome!' : 'Login successful!';
                window.utils.showNotification(successMessage, 'success');
                
                // Check if profile needs completion (name is missing)
                const user = response.data.user;
                if (!user.name || user.name.trim().length < 2) {
                    // Redirect to profile setup
                    setTimeout(() => {
                        window.location.href = '/profile';
                    }, 500);
                } else {
                    // Redirect to dashboard
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 500);
                }
            }
        } catch (error) {
            const errorMessage = error.response?.data?.message || error.response?.data?.errors?.otp?.[0] || 'Invalid OTP. Please try again.';
            showError('otp', errorMessage);
            resetOtpInputs();
            window.utils.showNotification(errorMessage, 'error');
        } finally {
            verifyOtpBtn.disabled = false;
            otpSpinner.classList.add('d-none');
        }
    });
    
    // Change Phone Number
    changePhoneBtn.addEventListener('click', function() {
        otpStep.classList.add('d-none');
        phoneStep.classList.remove('d-none');
        stopResendTimer();
        phoneInput.value = phoneNumber;
        phoneInput.focus();
    });
    
    // Resend OTP
    resendOtpBtn.addEventListener('click', async function() {
        resendOtpBtn.disabled = true;
        
        try {
            const response = await axios.post('/auth/otp', {
                phone: phoneNumber
            });
            
            if (response.data.success) {
                resetOtpInputs();
                startResendTimer();
                window.utils.showNotification('OTP resent successfully!', 'success');
            }
        } catch (error) {
            const errorMessage = error.response?.data?.message || 'Failed to resend OTP. Please try again.';
            window.utils.showNotification(errorMessage, 'error');
        } finally {
            resendOtpBtn.disabled = false;
        }
    });
    
    function showError(field, message) {
        const errorEl = document.getElementById(`${field}Error`);
        const inputEl = field === 'phone' ? phoneInput : otpInputs[0].closest('.otp-input-group');
        
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
        
        if (field === 'phone') {
            phoneInput.classList.add('is-invalid');
        } else {
            otpInputs.forEach(input => input.classList.add('is-invalid'));
        }
    }
    
    function clearError(field) {
        const errorEl = document.getElementById(`${field}Error`);
        if (errorEl) {
            errorEl.textContent = '';
            errorEl.style.display = 'none';
        }
        
        if (field === 'phone') {
            phoneInput.classList.remove('is-invalid');
        } else {
            otpInputs.forEach(input => input.classList.remove('is-invalid'));
        }
    }
    
    // Clear errors on input
    phoneInput.addEventListener('input', () => clearError('phone'));
    otpInputs.forEach(input => input.addEventListener('input', () => clearError('otp')));
    
    // Initialize
    initOtpInputs();
    
    // Focus first OTP input when OTP step is shown
    const observer = new MutationObserver(function(mutations) {
        if (!otpStep.classList.contains('d-none')) {
            otpInputs[0].focus();
        }
    });
    observer.observe(otpStep, { attributes: true, attributeFilter: ['class'] });
    
})();

