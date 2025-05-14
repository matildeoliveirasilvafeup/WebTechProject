export function checkEmailRequirements(email) {

    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

export function checkPasswordRequirements(password) {

    const requirements = {
        minLength: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        specialChar: /[!@#$%^&*(),.?":{}|<>_-]/.test(password)
    };

    updateRequirement('min-length', requirements.minLength);
    updateRequirement('uppercase', requirements.uppercase);
    updateRequirement('lowercase', requirements.lowercase);
    updateRequirement('number', requirements.number);
    updateRequirement('special-char', requirements.specialChar);

    return Object.values(requirements).every(Boolean);
}

function updateRequirement(id, isValid) {
    const item = document.getElementById(id);
    if (item) {
        const icon = item.querySelector('i');
        if (isValid) {
            item.classList.add('completed');
            item.classList.remove('failed');
            icon.classList.remove('fa-regular', 'fa-circle-check');
            icon.classList.add('fa-solid', 'fa-circle-check');
        } else {
            item.classList.add('failed');
            item.classList.remove('completed');
            icon.classList.remove('fa-solid', 'fa-circle-check');
            icon.classList.add('fa-regular', 'fa-circle-check');
        }
    }
}