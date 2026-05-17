document.querySelectorAll('.toggle-pass').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        input.type = input.type === 'password' ? 'text' : 'password';
    });
});