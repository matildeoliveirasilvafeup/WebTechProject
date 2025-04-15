document.addEventListener("DOMContentLoaded", function () {
    const tabLinks = document.querySelectorAll(".tab-link");
    const tabContents = document.querySelectorAll(".tab-content");

    function showTab(tabId) {
        // Remove classes
        tabLinks.forEach(link => link.classList.remove("active"));
        tabContents.forEach(content => content.classList.remove("active"));

        // Ativa o link e conteúdo correspondente
        const activeLink = document.querySelector(`.tab-link[data-tab="${tabId}"]`);
        const activeContent = document.getElementById(tabId);

        if (activeLink && activeContent) {
            activeLink.classList.add("active");
            activeContent.classList.add("active");
        }
    }

    tabLinks.forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const targetTab = this.getAttribute("data-tab");
            showTab(targetTab);
        });
    });

    // Ativa o primeiro tab por defeito se nenhum estiver ativo
    const firstTab = tabLinks[0]?.getAttribute("data-tab");
    if (firstTab) showTab(firstTab);
});

// Função de logout (associar a um botão ou onclick="logout()")
function logout() {
    if (confirm("Are you sure you want to log out?")) {
        window.location.href = "/authentication/logout.php";
    }
}
