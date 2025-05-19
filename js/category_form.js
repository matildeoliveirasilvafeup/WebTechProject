document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('subcategory-container');

    function updateInputs() {
        const inputs = Array.from(container.querySelectorAll('input[name="subcategories[]"]'));
        const lastInput = inputs[inputs.length - 1];

        if (lastInput && lastInput.value.trim() !== '') {
            const div = document.createElement('div');
            div.classList.add('subcategory-input');

            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.name = 'subcategories[]';
            newInput.placeholder = 'Subcategory name';

            newInput.addEventListener('input', updateInputs);

            div.appendChild(newInput);
            container.appendChild(div);
        }

        const emptyInputs = inputs.filter(input => input.value.trim() === '');
        if (emptyInputs.length > 1) {
            for (let i = 0; i < emptyInputs.length - 1; i++) {
                emptyInputs[i].parentElement.remove();
            }
        }

        const currentInputs = Array.from(container.querySelectorAll('input[name="subcategories[]"]'));
        currentInputs.forEach((input, index) => {
            input.required = index === 0;
        });
    }

    const existingInputs = container.querySelectorAll('input[name="subcategories[]"]');
    if (existingInputs.length === 0) {
        const initialDiv = document.createElement('div');
        initialDiv.classList.add('subcategory-input');

        const initialInput = document.createElement('input');
        initialInput.type = 'text';
        initialInput.name = 'subcategories[]';
        initialInput.placeholder = 'Subcategory name';

        initialInput.addEventListener('input', updateInputs);
        initialDiv.appendChild(initialInput);
        container.appendChild(initialDiv);
    } else {
        existingInputs.forEach(input => {
            input.addEventListener('input', updateInputs);
        });
    }

    updateInputs();
});