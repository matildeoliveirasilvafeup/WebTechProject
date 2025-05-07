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
  
function updateSlider() {
    const minSlider = document.getElementById('min-price')
    const maxSlider = document.getElementById('max-price')
    const minValue = document.getElementById('min-price-value')
    const maxValue = document.getElementById('max-price-value')
    const track = document.querySelector('.slider-track::before')

    let min = parseInt(minSlider.value)
    let max = parseInt(maxSlider.value)

    if (min > max - 10) {
        min = max - 10
        minSlider.value = min
    }

    if (max < min + 10) {
        max = min + 10
        maxSlider.value = max
    }

    minValue.textContent = `€${min}`
    maxValue.textContent = `€${max}`

    const rangeMin = parseInt(minSlider.min)
    const rangeMax = parseInt(maxSlider.max)

    const percentMin = ((min - rangeMin) / (rangeMax - rangeMin)) * 100
    const percentMax = ((max - rangeMin) / (rangeMax - rangeMin)) * 100

    const trackFill = document.querySelector('.slider-track::before')
    document.styleSheets[0].addRule('.slider-track::before', `left: ${percentMin}%; right: ${100 - percentMax}%;`)
}

window.addEventListener('DOMContentLoaded', updateSlider)