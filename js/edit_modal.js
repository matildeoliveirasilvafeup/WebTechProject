document.addEventListener("DOMContentLoaded", function () {

    /* ===== PROFILE EDITING ===== */

    const editProfBtn = document.getElementById("editProfBtn");
    const profileModal = document.getElementById("editProfileModal");
    const cancelEditProfile = document.getElementById("cancelEditProfile");
    const editProfileForm = document.getElementById("editProfileForm");

    if (editProfBtn && profileModal) {
        editProfBtn.addEventListener("click", () => {
            profileModal.classList.remove("hidden");
        });
    }

    if (cancelEditProfile && profileModal) {
        cancelEditProfile.addEventListener("click", () => {
            profileModal.classList.add("hidden");
        });
    }

    if (editProfileForm) {
        editProfileForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(editProfileForm);
            const name = formData.get("name");
            const loc = formData.get("location");
            const profilePicture = formData.get("profile_picture");

            console.log("Form data:", { name, loc, profilePicture });

            try {
                const response = await fetch("/actions/action_update_profile_details.php", {
                    method: "POST",
                    body: formData
                });

                const text = await response.text();
                console.log("Server response:", text);
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (err) {
                    console.error("Invalid JSON:", text);
                    alert("Unexpected response from server.");
                    return;
                }

                if (response.ok && result.success) {
                    notification.classList.remove("hidden");
                    setTimeout(() => {
                        notification.classList.add("hidden");
                        location.reload();
                    }, 1000);
                } else {
                    alert(result.message || "Failed to update profile.");
                }

            } catch (err) {
                console.error("Error submitting profile:", err);
                alert("An error occurred. Please try again.");
            }
        });
    }

    /* ===== BIO EDITING ===== */

    const editBioBtn = document.getElementById("editBioBtn");
    const editBioModal = document.getElementById("editBioModal");
    const cancelEditBio = document.getElementById("cancelEditBio");
    const editBioForm = document.getElementById("editBioForm");
    const bioText = document.getElementById("bioText");

    if (editBioBtn && editBioModal) {
        editBioBtn.addEventListener("click", () => {
            editBioModal.classList.remove("hidden");
        });
    }

    if (cancelEditBio) {
        cancelEditBio.addEventListener("click", () => {
            editBioModal.classList.add("hidden");
        });
    }

    if (editBioForm) {
        editBioForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(editBioForm);
            const bio = formData.get("bio");

            try {
                const response = await fetch("/actions/action_update_bio.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ bio })
                });

                const text = await response.text();
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (jsonError) {
                    console.error("Answer isn't JSON valid: ", text);
                    alert("Unexpected response from server.");
                    return;
                }

                if (result.success) {
                    bioText.textContent = bio;
                    editBioModal.classList.add("hidden");
                } else {
                    alert(result.message || "Error updating bio.");
                }

            } catch (error) {
                console.error("Something went wrong: ", error);
                alert("An error occurred. Please try again.");
            }
        });
    }

    /* ===== PREFERENCES EDITING ===== */

    const editPrefsBtn = document.getElementById("editPrefsBtn");
    const cancelPrefsBtn = document.getElementById("cancelEditPrefs");
    const modal = document.getElementById("editModalPrefs");
    const prefsForm = document.getElementById("preferencesForm");

    if (editPrefsBtn && cancelPrefsBtn && modal && prefsForm) {
        editPrefsBtn.addEventListener("click", () => {
            modal.classList.remove("hidden");
        });

        cancelPrefsBtn.addEventListener("click", () => {
            modal.classList.add("hidden");
        });

        prefsForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(prefsForm);

            const selectedDays = [...prefsForm.querySelectorAll('input[name="days"]:checked')].map(input => input.value);
            const startTime = prefsForm.querySelector('input[name="start_time"]').value;
            const endTime = prefsForm.querySelector('input[name="end_time"]').value;

            if (!startTime || !endTime || selectedDays.length === 0) {
                alert("Please select at least one day and both start/end times.");
                return;
            }

            const preferredDaysTimes = {
                days: selectedDays.map(day => ({
                    day,
                    time: `${formatTime(startTime)} - ${formatTime(endTime)}`
                }))
            };

            formData.append("preferred_days_times", JSON.stringify(preferredDaysTimes));

            try {
                const response = await fetch("/actions/action_update_preferences.php", {
                    method: "POST",
                    body: formData
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert("Failed to update preferences.");
                }
            } catch (err) {
                alert("An error occurred. Please try again.");
                console.error(err);
            }
        });
    }

    function formatTime(time) {
        const [hour, minute] = time.split(":");
        const h = parseInt(hour, 10);
        const ampm = h >= 12 ? "PM" : "AM";
        const hour12 = h % 12 === 0 ? 12 : h % 12;
        return `${hour12}:${minute} ${ampm}`;
    }
});