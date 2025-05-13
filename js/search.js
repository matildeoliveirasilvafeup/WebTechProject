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
            <input type="checkbox" name="subcategory[]" value="${sub.id}">
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

    searchInput.addEventListener('input', debounce(handleInput, 200)); 
});

document.addEventListener('DOMContentLoaded', () => {
    const filterForm = document.querySelector('.filter-form');
    const searchInput = document.getElementById('search-service-input');
    const serviceGrid = document.querySelector('.services-grid');
    const categorySelect = document.getElementById('category');
    const subcategoryContainer = document.getElementById('subcategory-container');
    const subcategoryCheckboxes = document.getElementById('subcategory-checkboxes');
    const sortSelect = filterForm.querySelector('select[name="sort"]');
    let debounceTimeout;

    const debounce = (callback, delay) => {
        return (...args) => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => callback(...args), delay);
        };
    };

    const fetchFilteredServices = async () => {
        const formData = new FormData(filterForm);

        const searchQuery = searchInput.value.trim();
        if (searchQuery) {
            formData.set('q', searchQuery);
        }

        const queryString = new URLSearchParams(formData).toString();

        const newUrl = new URL(window.location);
        newUrl.search = queryString;
        window.history.replaceState({}, '', newUrl);

        try {
            const response = await fetch(`../api/search_service.php?${queryString}`);
            const services = await response.json();

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

    const resetFiltersUI = () => {
        categorySelect.value = '';

        subcategoryContainer.style.display = 'none';
        subcategoryCheckboxes.innerHTML = '';

        const inputs = filterForm.querySelectorAll('input');
        inputs.forEach(input => {
            if (input.name === 'min_price') {
                input.value = '0';
            } else if (input.name === 'max_price') {
                input.value = '9999';
            } else if (input.type === 'number') {
                input.value = '';
            } else if (input.type === 'text') {
                input.value = '';
            } else if (input.type === 'checkbox') {
                input.checked = false; 
            }
        });

        const sortSelect = filterForm.querySelector('select[name="sort"]');
        if (sortSelect) {
            sortSelect.value = 'newest';
        }
    };

    const initializeFiltersFromURL = () => {
        const urlParams = new URLSearchParams(window.location.search);
    
        const searchQuery = urlParams.get('q') || '';
        searchInput.value = searchQuery;
    
        const category = urlParams.get('category');
        if (category) {
            categorySelect.value = category;
    
            const selectedOption = categorySelect.selectedOptions[0];
            const subcategories = JSON.parse(selectedOption?.dataset.subcategories || '[]');
        
            if (subcategories.length > 0) {
                subcategoryContainer.style.display = 'block';
    
                subcategoryCheckboxes.innerHTML = subcategories.map(sub =>
                    `<label>
                        <input type="checkbox" name="subcategory[]" value="${sub.id}">
                        ${sub.name}
                    </label>`
                ).join('');
            } else {
                subcategoryContainer.style.display = 'none';
                subcategoryCheckboxes.innerHTML = '';
            }

            const selectedSubcategories = urlParams.getAll('subcategory[]');
            selectedSubcategories.forEach(id => {
                const checkbox = subcategoryCheckboxes.querySelector(`input[type="checkbox"][value="${id}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        } else {
            categorySelect.value = '';
            subcategoryContainer.style.display = 'none';
            subcategoryCheckboxes.innerHTML = '';
        }
    
        const inputs = filterForm.querySelectorAll('input, select');
        inputs.forEach(input => {
            if (input.name !== 'subcategory[]') {
                const value = urlParams.get(input.name);
                if (value !== null) {
                    if (input.type === 'checkbox') {
                        input.checked = urlParams.getAll(input.name).includes(input.value);
                    } else {
                        input.value = value;
                    }
                }
            }
        });
        fetchFilteredServices();
    };

    const addDynamicFilterListeners = () => {
        const inputs = filterForm.querySelectorAll('input, select');
        const debouncedFetch = debounce(fetchFilteredServices, 300);
        inputs.forEach(input => {
            input.addEventListener('input', debouncedFetch); 
            input.addEventListener('change', debouncedFetch);
        });

        subcategoryCheckboxes.addEventListener('change', debouncedFetch);

        searchInput.addEventListener('input', () => {
            resetFiltersUI();
            fetchFilteredServices();
        });

        if (sortSelect) {
            sortSelect.addEventListener('change', debouncedFetch);
        }
    };

    initializeFiltersFromURL();
    addDynamicFilterListeners();
});