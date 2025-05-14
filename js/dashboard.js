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
});

function logout() {
    if (confirm("Are you sure you want to log out?")) {
        window.location.href = "/actions/action_logout.php";
    }
}
