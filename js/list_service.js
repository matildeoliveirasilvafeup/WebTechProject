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

document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("images");
    const newMediaPreview = document.getElementById("new-media-preview");

    input.addEventListener("change", () => {
        newMediaPreview.innerHTML = "";

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

            newMediaPreview.appendChild(wrapper);
        });
    });
});

document.querySelector('.create-form').addEventListener('submit', function (event) {
    const form = event.target;

    const newFiles = form.querySelector('#images').files;
    const newImageCount = Array.from(newFiles).filter(file => file.type.startsWith('image/')).length;

    const oldMedia = form.querySelectorAll('#old-media-preview .file-item');
    let oldImageCount = 0;

    oldMedia.forEach(item => {
        const isImage = item.dataset.isImage === "true";
        const checkbox = item.querySelector('input[type="checkbox"]');
        const markedForDeletion = checkbox?.checked;

        if (isImage && !markedForDeletion) {
            oldImageCount++;
        }
    });

    const totalImageCount = oldImageCount + newImageCount;

    if (totalImageCount === 0) {
        alert("You need to add at least one image in order to publish a service.");
        event.preventDefault();
        return
    } else if (totalImageCount > 15) {
        alert("You can only have a maximum of 15 media files per service.");
        event.preventDefault();
        return;
    }

    const totalUploadSize = Array.from(newFiles).reduce((sum, file) => sum + file.size, 0);

    const MAX_UPLOAD_SIZE = 8 * 1024 * 1024;

    if (totalUploadSize > MAX_UPLOAD_SIZE) {
        alert("Total file size exceeds the 8MB limit. Please reduce the size of your images or videos.");
        event.preventDefault();
        return;
    }
});