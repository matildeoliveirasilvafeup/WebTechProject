let showing;

function togglePassword(icon, id) {

    if (icon.querySelector('i')?.classList.contains('fa-eye-slash')) {
        showing = false;
    } else if (icon.querySelector('i')?.classList.contains('fa-eye')){
        showing = true;
    }

    const input = document.getElementById(id);
    
    input.dataset.realValue = input.value; 

    const realValue = input.dataset.realValue;
    let display = input.value;
    let i = 0;

    clearInterval(input.dataset.intervalId);

    const interval = setInterval(() => {
        if (!showing) {
            display = realValue.substring(0, i + 1) + '•'.repeat(realValue.length - i - 1);
        } else {
            display = '•'.repeat(i) + realValue.substring(i + 1, realValue.length);
        }

        input.type = 'text';
        input.value = display;
        i++;

        if (i >= realValue.length) {
            clearInterval(interval);
            showing = !showing;

            if (showing) {
                input.value = realValue;
            } else {
                input.type = 'password';
                input.value = realValue;
            }

            icon.innerHTML = showing ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        }
    }, 50);

    input.dataset.intervalId = interval;
}