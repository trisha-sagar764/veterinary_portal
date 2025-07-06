// Font size adjustment
function increaseFontSize() {
    document.body.style.fontSize = '20px';
}

function normalFontSize() {
    document.body.style.fontSize = '16px';
}

function decreaseFontSize() {
    document.body.style.fontSize = '14px';
}

// Password visibility toggle
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Basic input sanitization
function sanitizeInput(input) {
    return input.replace(/[^a-zA-Z0-9_@.#$%^&*! -]/g, '');
}