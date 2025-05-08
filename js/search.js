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

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-service-input');
    const serviceGrid = document.querySelector('.services-grid');
    let debounceTimeout;

    const fetchServices = async (query) => {
        try {
            const response = await fetch(`../api/search_service.php?q=${encodeURIComponent(query)}`);
            const services = await response.json();

            console.log('Services fetched:', services);

            serviceGrid.innerHTML = '';

            services.forEach(service => {
                const imageUrl = service.mediaUrl || 'https://via.placeholder.com/300';
                const freelancerName = service.freelancerName || 'Unknown Freelancer';
                const serviceCard = `
                    <a href="service.php?id=${service.id}" class="service-card">
                        <img src="${imageUrl}" alt="Service image">
                        <div class="service-info">
                            <h3>${service.title}</h3>
                            <p class="freelancer">By ${freelancerName}</p>
                            <p class="price">€${service.price.toFixed(2)}</p>
                        </div>
                    </a>
                `;
                serviceGrid.insertAdjacentHTML('beforeend', serviceCard);
            });
        } catch (error) {
            console.error('Error fetching services:', error);
        }
    };

    const debounce = (callback, delay) => {
        return (...args) => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => callback(...args), delay);
        };
    };

    const handleInput = (event) => {
        const query = event.target.value.trim();

        const newUrl = new URL(window.location);
        newUrl.searchParams.set('q', query);
        window.history.replaceState({}, '', newUrl);

        fetchServices(query);
    };

    searchInput.addEventListener('input', debounce(handleInput, 300)); 
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