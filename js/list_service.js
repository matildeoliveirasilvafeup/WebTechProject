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