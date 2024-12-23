function validateUsername(username) {
    if (!username || username.trim().length ==0) {
        return 'Username required';
    }
    return '';
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !emailRegex.test(email)) {
        return 'Please enter a valid email address';
    }
    return '';
}

function validateEditForm(event) {
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    let isValid = true;
    
    // Clear previous error messages
    document.querySelectorAll('.error').forEach(error => error.textContent = '');
    
    // Validate username
    const usernameError = validateUsername(username);
    if (usernameError) {
        document.getElementById('username-error').textContent = usernameError;
        isValid = false;
    }
    
    // Validate email
    const emailError = validateEmail(email);
    if (emailError) {
        document.getElementById('email-error').textContent = emailError;
        isValid = false;
    }
    
    if (!isValid) {
        event.preventDefault();
    }
}

// Add event listener when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', validateEditForm);
    }
});
