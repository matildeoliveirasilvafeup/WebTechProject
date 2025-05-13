import { checkEmailRequirements, checkPasswordRequirements } from './form_utils.js';

const nameInput = document.getElementById('name');
const usernameInput = document.getElementById('username');
const passwordInput = document.getElementById('password');
const emailInput = document.getElementById('email');
const Btn = document.getElementById('btn');

const container = document.querySelector('.auth-container');
const signType = container?.dataset.formType || '';

console.log()

const emailStatus = document.getElementById('email-status');
const usernameStatus = document.getElementById('username-status');

let emailAvailable = false;
let usernameAvailable = false;

passwordInput.addEventListener('input', () => {

    checkFormValidity();

});

emailInput.addEventListener('input', () => {
    const email = emailInput.value.trim();

    if (checkEmailRequirements(email)) {
        fetch(`../api/validate_user.php?email=${encodeURIComponent(email)}`)
        .then(res => res.json())
        .then(data => {
            checkFormValidity();
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
        checkFormValidity();
        emailStatus.textContent = 'Invalid email format';
        emailStatus.style.color = 'gray';
        emailAvailable = false;
    }
    
    checkFormValidity();
});

usernameInput.addEventListener('input', () => {
    const username = usernameInput.value.trim();

    if (username) {
        fetch(`../api/validate_user.php?username=${encodeURIComponent(username)}`)
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
                checkFormValidity();
            });
    } else {
        usernameStatus.textContent = '';
        usernameAvailable = false;
        checkFormValidity();
    }
    
    checkFormValidity();
});

nameInput.addEventListener('input', checkFormValidity);

function checkNameRequirements(name) {

    return name.length >= 1;
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

    if (isNameValid && usernameAvailable && isPasswordValid && emailAvailable) {
        Btn.removeAttribute('disabled');
    } else {
        Btn.setAttribute('disabled', 'true');
    }
}

function checkFormValidity() {
    if (signType == 'signin') {
        checkSignInFormValidity();
    } else if (signType == 'signup') {
        checkSignUpFormValidity();
    }
}