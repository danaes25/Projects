const inputs = document.querySelectorAll('.input-group input');
inputs.forEach(input => {
    input.addEventListener('focus', () => {
        input.parentNode.querySelector('label').style.color = 'var(--organic)';
    });
    
    input.addEventListener('blur', () => {
        input.parentNode.querySelector('label').style.color = 'var(--cocoa)';
    });
});
