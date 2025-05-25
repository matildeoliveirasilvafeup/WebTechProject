document.addEventListener('DOMContentLoaded', () => {
    const categorySelect = document.getElementById('category');
    const subcategoryContainer = document.getElementById('subcategory-container');
    const subcategoryCheckboxes = document.getElementById('subcategory-checkboxes');
    const searchInput = document.getElementById('search-service-input');
    const serviceGrid = document.querySelector('.services-grid');
    const filterForm = document.querySelector('.filter-form');
    const paginationContainer = document.querySelector('.pagination');
    const sortSelect = filterForm.querySelector('select[name="sort"]');
    let debounceTimeout;

    const debounce = (callback, delay) => {
        return (...args) => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => callback(...args), delay);
        };
    };

    const fetchServices = async (page = 1) => {
        const formData = new FormData(filterForm);
        formData.set('q', searchInput.value.trim());
        formData.set('page', page);

        const queryString = new URLSearchParams(formData).toString();
        const newUrl = new URL(window.location);
        newUrl.search = queryString;
        window.history.replaceState({}, '', newUrl);

        try {
            const response = await fetch(`../api/search_service.php?${queryString}`);
            const data = await response.json();

            serviceGrid.innerHTML = '';
            (data.services || data).forEach(service => {
                const images = service.mediaUrls || ['https://via.placeholder.com/300'];
                const freelancerName = service.freelancerName || 'Unknown Freelancer';
                const serviceCard = `
                    <a href="service.php?id=${service.id}" class="service-card">
                        <div class="service-images">
                            <img src="${images[0]}" alt="Service image">
                        </div>
                        <div class="service-info">
                            <h3>${service.title}</h3>
                            <p class="freelancer">By ${freelancerName}</p>
                            <p class="price">€${service.price.toFixed(2)}</p>
                        </div>
                    </a>
                `;
                serviceGrid.insertAdjacentHTML('beforeend', serviceCard);
            });

            if (paginationContainer && data.totalPages) {
                let pagHtml = '';
                for (let i = 1; i <= data.totalPages; i++) {
                    pagHtml += i === data.page
                        ? `<span class="current">${i}</span>`
                        : `<a href="#" data-page="${i}">${i}</a>`;
                }
                paginationContainer.innerHTML = pagHtml;
            }
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
            if (input.name === 'min_price') input.value = '0';
            else if (input.name === 'max_price') input.value = '9999';
            else if (input.type === 'number' || input.type === 'text') input.value = '';
            else if (input.type === 'checkbox') input.checked = false;
        });

        if (sortSelect) sortSelect.value = 'newest';
    };

    const initializeFiltersFromURL = () => {
        const urlParams = new URLSearchParams(window.location.search);
        searchInput.value = urlParams.get('q') || '';

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
            }

            const selectedSubcategories = urlParams.getAll('subcategory[]');
            selectedSubcategories.forEach(id => {
                const checkbox = subcategoryCheckboxes.querySelector(`input[value="${id}"]`);
                if (checkbox) checkbox.checked = true;
            });
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
    };

    const handleCategoryChange = () => {
        const selectedOption = categorySelect.selectedOptions[0];
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
    };

    const addDynamicFilterListeners = () => {
        const debouncedFetch = debounce(() => fetchServices(1), 300);
        const inputs = filterForm.querySelectorAll('input, select');

        inputs.forEach(input => {
            input.addEventListener('input', debouncedFetch);
            input.addEventListener('change', debouncedFetch);
        });

        subcategoryCheckboxes.addEventListener('change', debouncedFetch);

        searchInput.addEventListener('input', () => {
            resetFiltersUI();
            fetchServices(1);
        });

        if (sortSelect) {
            sortSelect.addEventListener('change', debouncedFetch);
        }

        if (paginationContainer) {
            paginationContainer.addEventListener('click', (e) => {
                if (e.target.tagName === 'A' && e.target.dataset.page) {
                    e.preventDefault();
                    const page = parseInt(e.target.dataset.page, 10);
                    fetchServices(page);
                }
            });
        }
    };

    // Iniciar
    categorySelect.addEventListener('change', handleCategoryChange);
    initializeFiltersFromURL();
    addDynamicFilterListeners();
    fetchServices(1);
});