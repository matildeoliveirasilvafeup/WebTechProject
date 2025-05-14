let isFavorited;

async function toggleFavorite(icon, listingId) {

    if (icon.querySelector('i')?.classList.contains('fa-regular')) {
        isFavorited = true;
    } else if (icon.querySelector('i')?.classList.contains('fa-solid')){
        isFavorited = false;
    }

    icon.innerHTML = isFavorited ? '<i class="fa-solid fa-heart"></i>' : '<i class="fa-regular fa-heart"></i>';

    const data = new FormData();
    data.append('id', listingId);
    data.append('action', isFavorited ? 'add' : 'remove');

    try {
        const response = await fetch("/actions/action_manage_favorite.php", {
            method: "POST",
            body: data
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

        if (!response.ok || !result.success) {
            alert(result.message || "Failed to manage favorites.");
        }

    } catch (err) {
        console.error("Error managing favorites:", err);
        alert("An error occurred. Please try again.");
    }
}