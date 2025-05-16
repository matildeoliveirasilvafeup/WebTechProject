document.getElementById('category').addEventListener('change', function () {
    const subcategorySelect = document.getElementById('subcategory');
    const selectedOption = this.options[this.selectedIndex];
    const subcategories = JSON.parse(selectedOption.getAttribute('data-subcategories') || '[]');

    subcategorySelect.innerHTML = '<option value="" disabled selected>Select a subcategory</option>';
    if (subcategories.length > 0) {
        subcategories.forEach(subcategory => {
            const option = document.createElement('option');
            option.value = subcategory.id;
            option.textContent = subcategory.name;
            subcategorySelect.appendChild(option);
        });
        subcategorySelect.disabled = false;
    } else {
        subcategorySelect.disabled = true;
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("images");
    const previewContainer = document.getElementById("file-preview");

    input.addEventListener("change", () => {
        previewContainer.innerHTML = "";

        Array.from(input.files).forEach(file => {
            const fileType = file.type;
            const fileURL = URL.createObjectURL(file);

            const wrapper = document.createElement("div");
            wrapper.className = "file-item";

            if (fileType.startsWith("image/")) {
                const img = document.createElement("img");
                img.src = fileURL;
                img.alt = file.name;
                wrapper.appendChild(img);
            } else if (fileType.startsWith("video/")) {
                const video = document.createElement("video");
                video.src = fileURL;
                video.controls = true;
                video.width = 200;
                wrapper.appendChild(video);
            } else {
                const msg = document.createElement("p");
                msg.textContent = "Unsupported file: " + file.name;
                wrapper.appendChild(msg);
            }

            previewContainer.appendChild(wrapper);
        });
    });
});
