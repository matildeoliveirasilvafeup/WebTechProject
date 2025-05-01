function scrollSlider(button, direction) {
    const wrapper = button.closest('.services-slider-wrapper');
    const slider = wrapper.querySelector('.services-slider');
    const scrollAmount = slider.offsetWidth * 0.8;
    slider.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
}

