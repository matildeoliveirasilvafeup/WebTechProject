function scrollSlider(direction) {
    const slider = document.getElementById('servicesSlider');
    const scrollAmount = slider.offsetWidth * 0.8;
    slider.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
}

