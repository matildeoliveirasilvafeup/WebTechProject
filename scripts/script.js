/* ===== PASSWORD TOGGLE ===== */

let showing = false;

function togglePassword(icon) {
    const input = document.getElementById('password');
    
    input.dataset.realValue = input.value; 

    const realValue = input.dataset.realValue;
    let display = input.value;
    let i = 0;

    clearInterval(input.dataset.intervalId);

    const interval = setInterval(() => {
        if (!showing) {
            display = realValue.substring(0, i + 1) + '•'.repeat(realValue.length - i - 1);
        } else {
            display = '•'.repeat(i) + realValue.substring(i + 1, realValue.length);
        }

        input.type = 'text';
        input.value = display;
        i++;

        if (i >= realValue.length) {
            clearInterval(interval);
            showing = !showing;

            if (showing) {
                input.value = realValue;
            } else {
                input.type = 'password';
                input.value = realValue;
            }

            icon.innerHTML = showing ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        }
    }, 50);

    input.dataset.intervalId = interval;
}


/* ===== FORM SETUP ===== */

const usernameInput = document.getElementById('username');
const passwordInput = document.getElementById('password');
const emailInput = document.getElementById('email');
const Btn = document.getElementById('btn');

passwordInput.addEventListener('input', function () {
    checkSignInFormValidity();
    checkSignUpFormValidity();
});

emailInput.addEventListener('input', function () {
    checkSignInFormValidity();
    checkSignUpFormValidity();
});
usernameInput.addEventListener('input', function () {
    checkSignUpFormValidity();
});

function checkEmailRequirements(email) {

    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function checkPasswordRequirements(password) {
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

function checkSignInFormValidity() {
    const email = emailInput.value.trim();
    const password = passwordInput.value;

    const isEmailValid = checkEmailRequirements(email);

    if (isEmailValid && password.length >= 1) {
        Btn.removeAttribute('disabled');
    } else {
        Btn.setAttribute('disabled', 'true');
    }
}

function checkSignUpFormValidity() {
    const isUsernameValid = usernameInput.value.trim();
    const email = emailInput.value.trim();
    const password = passwordInput.value;
    

    const isEmailValid = checkEmailRequirements(email);
    let isPasswordValid = checkPasswordRequirements(password);

    if (isUsernameValid && isEmailValid && isPasswordValid) {
        Btn.removeAttribute('disabled');
    } else {
        Btn.setAttribute('disabled', 'true');
    }
}