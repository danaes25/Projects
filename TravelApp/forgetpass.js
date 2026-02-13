document.addEventListener('DOMContentLoaded', function() {
    // Store the original form HTML
    const forgotPasswordContainer = document.querySelector('.forgot-password-container');
    const originalFormHTML = forgotPasswordContainer.innerHTML;
    
    // Get DOM elements
    const inputField = document.querySelector('.input-field');
    const submitBtn = document.querySelector('.submit-btn');
    const secondaryLink = document.querySelector('.secondary-link');
    const createAccountLink = document.querySelector('.create-account');
    const backToLoginLink = document.querySelector('.back-to-login a');

    // Submit button click handler
    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const userInput = inputField.value.trim();
        
        // Validate input
        if (!userInput) {
            showError('Please enter your email, phone, or username');
            return;
        }
        
        // Check if it's a valid email (simple validation)
        if (userInput.includes('@') && !isValidEmail(userInput)) {
            showError('Please enter a valid email address');
            return;
        }
        
        // Simulate sending reset link
        simulateResetLink(userInput);
    });

    // Secondary link click handler
    secondaryLink.addEventListener('click', function(e) {
        e.preventDefault();
        alert('Please contact our support team at support@exploremycountry.com for further assistance.');
    });

    // Create account link click handler
    createAccountLink.addEventListener('click', function(e) {
        e.preventDefault();
        alert('Redirecting to account creation page...');
    });

    // Back to login link click handler
    backToLoginLink.addEventListener('click', function(e) {
        e.preventDefault();
        alert('Redirecting to login page...');
    });

    // Helper function to validate email
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Helper function to show error message
    function showError(message) {
        // Remove any existing error messages
        const existingError = document.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Create error element
        const errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        errorElement.style.color = '#d9534f';
        errorElement.style.marginBottom = '15px';
        errorElement.style.fontSize = '0.9rem';
        errorElement.textContent = message;
        
        // Insert error message above the input field
        inputField.parentNode.insertBefore(errorElement, inputField.nextSibling);
        
        // Highlight the input field
        inputField.style.borderColor = '#d9534f';
    }

    // Simulate sending reset link
    function simulateResetLink(userInput) {
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        
        // Simulate network delay
        setTimeout(function() {
            // Create success message
            const successHTML = `
                <div class="success-message">
                    <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--organic); margin-bottom: 15px;"></i>
                    <h2 style="color: var(--palm-oil); margin-bottom: 15px;">Reset Link Sent!</h2>
                    <p style="color: var(--cocoa); margin-bottom: 25px;">
                        We've sent a password reset link to <strong>${userInput}</strong>. 
                        Please check your inbox and follow the instructions.
                    </p>
                    <button class="submit-btn" id="return-to-login" style="margin-top: 10px;">
                        Return to Login
                    </button>
                </div>
            `;
            
            // Replace form with success message
            forgotPasswordContainer.innerHTML = successHTML;
            
            // Add click handler for return to login button
            document.getElementById('return-to-login').addEventListener('click', function() {
                // Restore the original form
                forgotPasswordContainer.innerHTML = originalFormHTML;
                
                // Reinitialize event listeners
                initializeEventListeners();
            });
        }, 1500);
    }

    // Function to reinitialize event listeners after restoring form
    function initializeEventListeners() {
        const inputField = document.querySelector('.input-field');
        const submitBtn = document.querySelector('.submit-btn');
        const secondaryLink = document.querySelector('.secondary-link');
        const createAccountLink = document.querySelector('.create-account');
        const backToLoginLink = document.querySelector('.back-to-login a');

        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const userInput = inputField.value.trim();
            
            if (!userInput) {
                showError('Please enter your email, phone, or username');
                return;
            }
            
            if (userInput.includes('@') && !isValidEmail(userInput)) {
                showError('Please enter a valid email address');
                return;
            }
            
            simulateResetLink(userInput);
        });

        secondaryLink.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Please contact our support team at support@exploremycountry.com for further assistance.');
        });

        createAccountLink.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Redirecting to account creation page...');
        });

        backToLoginLink.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Redirecting to login page...');
        });
    }
});