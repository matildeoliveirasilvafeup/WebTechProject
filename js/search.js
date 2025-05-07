document.addEventListener('DOMContentLoaded', () => {
    const categorySelect = document.getElementById('category');
    const subcategoryContainer = document.getElementById('subcategory-container');
    const subcategoryCheckboxes = document.getElementById('subcategory-checkboxes');
  
    categorySelect.addEventListener('change', function () {
      const selectedOption = this.selectedOptions[0];
      const subcategories = JSON.parse(selectedOption.dataset.subcategories || '[]');
  
      if (subcategories.length > 0) {
        subcategoryContainer.style.display = 'block';
        subcategoryCheckboxes.innerHTML = subcategories.map(sub =>
          `<label>
            <input type="checkbox" name="subcategory[]" value="${sub.name}">
            ${sub.name}
          </label>`
        ).join('');
      } else {
        subcategoryContainer.style.display = 'none';
        subcategoryCheckboxes.innerHTML = '';
      }
    });
});
  