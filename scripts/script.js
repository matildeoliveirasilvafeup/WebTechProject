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

const nameInput = document.getElementById('name');
const usernameInput = document.getElementById('username');
const passwordInput = document.getElementById('password');
const emailInput = document.getElementById('email');
const Btn = document.getElementById('btn');

const container = document.querySelector('.login-container') || document.querySelector('.sign-up-container');
const signType = container?.dataset.formType || '';

console.log()

const emailStatus = document.getElementById('email-status');
const usernameStatus = document.getElementById('username-status');

let emailAvailable = false;
let usernameAvailable = false;

passwordInput.addEventListener('input', () => {

    if (signType == 'signin') {
        checkSignInFormValidity();
    } else if (signType == 'signup') {
        checkSignUpFormValidity();
    }

});

emailInput.addEventListener('input', () => {
    const email = emailInput.value.trim();
    
    if (checkEmailRequirements(email)) {
        fetch(`../database/register.php?email=${encodeURIComponent(email)}`)
        .then(res => res.json())
        .then(data => {
            if (data.email?.used) {
                emailStatus.textContent = 'Email already in use';
                emailStatus.style.color = 'red';
                emailAvailable = false;
            } else {
                emailStatus.textContent = '';
                emailAvailable = true;
            }
        });
    } else {
        emailStatus.textContent = 'Invalid email format';
        emailStatus.style.color = 'gray';
        emailAvailable = false;
    }
    
    if (signType == 'signin') {
        checkSignInFormValidity();
    } else if (signType == 'signup') {
        checkSignUpFormValidity();
    }
});

usernameInput.addEventListener('input', () => {
    const username = usernameInput.value.trim();

    if (username) {
        fetch(`../database/register.php?username=${encodeURIComponent(username)}`)
            .then(res => res.json())
            .then(data => {
                if (data.username?.used) {
                    usernameStatus.textContent = 'Username already taken';
                    usernameStatus.style.color = 'red';
                    usernameAvailable = false;
                } else {
                    usernameStatus.textContent = 'Username available';
                    usernameStatus.style.color = 'green';
                    usernameAvailable = true;
                }
            });
    } else {
        usernameStatus.textContent = '';
        usernameAvailable = false;
    }

    checkSignUpFormValidity();
});

nameInput.addEventListener('input', checkSignUpFormValidity);

function checkEmailRequirements(email) {

    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function checkNameRequirements(name) {

    return name.length >= 1;
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
    const name = nameInput.value.trim();
    const password = passwordInput.value;

    const isNameValid = checkNameRequirements(name);
    const isPasswordValid = checkPasswordRequirements(password);

    if (isNameValid  && usernameAvailable && isPasswordValid && emailAvailable) {
        Btn.removeAttribute('disabled');
    } else {
        Btn.setAttribute('disabled', 'true');
    }
}