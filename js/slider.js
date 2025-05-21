function scrollSlider(button, direction) {
    const wrapper = button.closest('.services-slider-wrapper');
    const slider = wrapper.querySelector('.services-slider');
    const scrollAmount = slider.offsetWidth * 0.8;
    slider.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
}

function scrollCategorySlider(button, direction) {
    const wrapper = button.closest('.carousel-wrapper');
    const slider = wrapper.querySelector('.category-carousel');
    const scrollAmount = slider.offsetWidth * 0.8;
    slider.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
}

document.addEventListener("DOMContentLoaded", function () {
    const carousel = document.querySelector(".category-carousel");

    if (carousel) {
        if (carousel.scrollWidth > carousel.clientWidth) {
            carousel.classList.add("scrollable");
        } else {
            carousel.classList.remove("scrollable");
        }

        window.addEventListener("resize", function () {
            if (carousel.scrollWidth > carousel.clientWidth) {
                carousel.classList.add("scrollable");
            } else {
                carousel.classList.remove("scrollable");
            }
        });
    }
});
