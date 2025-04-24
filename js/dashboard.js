function showTab(tabId) {
    const tabLinks = document.querySelectorAll(".tab-link");
    const tabContents = document.querySelectorAll(".tab-content");

    tabLinks.forEach(link => link.classList.remove("active"));
    tabContents.forEach(content => content.classList.remove("active"));

    const activeLink = document.querySelector(`.tab-link[data-tab="${tabId}"]`);
    const activeContent = document.getElementById(tabId);

    if (activeLink && activeContent) {
        activeLink.classList.add("active");
        activeContent.classList.add("active");
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const tabLinks = document.querySelectorAll(".tab-link");

    tabLinks.forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const targetTab = this.getAttribute("data-tab");

            if (targetTab) {
                showTab(targetTab);
                history.replaceState(null, "", `#${targetTab}`);
            }
        });
    });

    const initialTab = window.location.hash.substring(1) || tabLinks[0]?.getAttribute("data-tab");
    if (initialTab) showTab(initialTab);

    /* ===== PROFILE EDITING ===== */
    
    const editProfBtn = document.getElementById("editProfBtn");
    const profileModal = document.getElementById("editProfileModal");
    const cancelEditProfile = document.getElementById("cancelEditProfile");

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
            const bio = document.getElementById("editBio").value;

            if (!bio.trim()) {
                alert("Bio cannot be empty.");
                return;
            }

            const formData = new FormData();
            formData.append("bio", bio);

            try {
                const response = await fetch("/database/update_bio.php", {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    bioText.textContent = bio;
                    editBioModal.classList.add("hidden");
                } else {
                    alert(result.message || "Failed to update bio.");
                }
            } catch (err) {
                alert("An error occurred. Please try again.");
                console.error(err);
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
                const response = await fetch("/database/update_preferences.php", {
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

function logout() {
    if (confirm("Are you sure you want to log out?")) {
        window.location.href = "/authentication/logout.php";
    }
}
