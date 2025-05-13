import { checkEmailRequirements, checkPasswordRequirements } from './form_utils.js';

document.addEventListener('DOMContentLoaded', function () {

    const notification = document.getElementById("notification");
    
    const editEmailForm = document.getElementById("editEmailForm");
    const saveBtnEmail = document.getElementById('save-btn email');
    const editPasswordForm = document.getElementById("editPasswordForm");
    const saveBtnPassword = document.getElementById('save-btn password');
    const deactivateBtn = document.getElementById('deactivate-btn');

    const passwordInput = document.getElementById('password');
    const newPasswordInput = document.getElementById('new-password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const emailInput = document.getElementById('email');

    if (passwordInput) {
        passwordInput.addEventListener('input', checkFormValidityPassword);
    }
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', checkFormValidityPassword);
    }
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', checkFormValidityPassword);
    }
    if (emailInput) {
        emailInput.addEventListener('input', checkFormValidityEmail);
    }

    if (deactivateBtn) {
        deactivateBtn.addEventListener('click', async (e) => {
            e.preventDefault();
        
            const reason = document.getElementById("reason").value;
            if (!reason || reason === "Choose a reason") {
                alert("Please select a reason before continuing.");
                return;
            }

            const data = new FormData();
            data.append('reason', reason);

            try {
                const response = await fetch("/actions/action_delete_account.php", {
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
                    window.location.href = '/index.php';
                } else {
                    alert(result.message || "Failed to deactivate account.");
                }
            } catch (err) {
                alert("An error occurred. Please try again.");
                console.error(err);
            }
        });
    }
    
    if (editEmailForm) {
        editEmailForm.addEventListener('submit', async (e) => {
            e.preventDefault();
    
            const email = document.getElementById("email").value;
            const data = new FormData();
            data.append('email', email);
    
            try {
                const response = await fetch("/actions/action_update_authentication.php", {
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
                    alert(result.message || "Failed to update email.");
                }
            } catch (err) {
                alert("An error occurred. Please try again.");
                console.error(err);
            }
        });
    }

    if (editPasswordForm) {
        editPasswordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
    
            const password = document.getElementById("password").value;
            const newPassword = document.getElementById("new-password").value;
    
            const data = new FormData();
            data.append('password', password);
            data.append('newPassword', newPassword);
    
            try {
                const response = await fetch("/actions/action_update_authentication.php", {
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
                    alert(result.message || "Failed to update password.");
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
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value.trim();
        
        const validPassword = checkPasswordRequirements(newPassword);
        const differentPasswords = newPassword !== password;
        const passwordsMatch = confirmPassword === newPassword;

        if (validPassword && passwordsMatch && differentPasswords) {
            saveBtnPassword.removeAttribute('disabled');
        } else {
            saveBtnPassword.setAttribute('disabled', 'true');
        }
    }

});
