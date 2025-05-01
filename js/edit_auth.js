// import { checkEmailRequirements, checkPasswordRequirements } from './form.js';

document.addEventListener('DOMContentLoaded', function () {

    const notification = document.getElementById("notification");
    
    const editEmailForm = document.getElementById("editEmailForm");
    const saveBtnEmail = document.getElementById('save-btn email');
    const editPasswordForm = document.getElementById("editPasswordForm");
    const saveBtnPassword = document.getElementById('save-btn password');

    const passwordInput = document.getElementById('password');
    const newPasswordInput = document.getElementById('new-password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const emailInput = document.getElementById('email');

    passwordInput.addEventListener('input', () => {
        checkFormValidityPassword();
    });
    newPasswordInput.addEventListener('input', () => {
        checkFormValidityPassword();
    });
    confirmPasswordInput.addEventListener('input', () => {
        checkFormValidityPassword();
    });
    emailInput.addEventListener('input', () => {        
        checkFormValidityEmail();
    });

    if (editEmailForm) {
        editEmailForm.addEventListener('submit', async (e) => {
            e.preventDefault();
    
            const email = document.getElementById("email").value;
            const data = new FormData();
            data.append('email', email);
    
            try {
                const response = await fetch("/database/update_authentication.php", {
                    method: "POST",
                    body: data
                });
    
                const text = await response.text();
                let result;
    
                try {
                    result = JSON.parse(text);
                } catch (err) {
                    console.error("Invalid JSON:", text);
                    alert("Unexpected server response.");
                    return;
                }
    
                if (response.ok && result.success) {
                    notification.classList.remove("hidden");
                
                    setTimeout(() => {
                        notification.classList.add("hidden");
                        location.reload();
                    }, 1000);
                } else {
                    alert(result.message || "Failed to update auth.");
                }
            } catch (err) {
                alert("An error occurred. Please try again.");
                console.error(err);
            }
        });
    }

    function checkFormValidityEmail() {
        const email = emailInput.value.trim();

        const validEmail = checkEmailRequirements(email);

        if (validEmail) {
            saveBtnEmail.removeAttribute('disabled');
        } else {
            saveBtnEmail.setAttribute('disabled', 'true');
        }
    }

    function checkFormValidityPassword() {
        const password = passwordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();
        
        const validPassword = checkPasswordRequirements(password);
        const passwordsMatch = password === confirmPassword;

        if (validPassword && passwordsMatch) {
            saveBtnPassword.removeAttribute('disabled');
        } else {
            saveBtnPassword.setAttribute('disabled', 'true');
        }
    }

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
});
