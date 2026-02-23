document.addEventListener('DOMContentLoaded', () => {
    // Select the password input and the toggle button
    const passwordInput = document.getElementById('password');
    const togglePasswordBtn = document.getElementById('togglePassword');

    // Add click event listener to the toggle button
    togglePasswordBtn.addEventListener('click', function () {
        // Check the current type of the password field
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        
        // Toggle the type attribute
        passwordInput.setAttribute('type', type);

        // Optional: Toggle the eye icon visual state (e.g., adding a slash across the eye)
        if (type === 'text') {
            this.style.opacity = '0.5';
        } else {
            this.style.opacity = '1';
        }
    });
});