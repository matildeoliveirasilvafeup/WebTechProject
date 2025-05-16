function scrollMedia(direction) {
    const wrapper = document.querySelector('.carousel-wrapper');
    const scrollAmount = wrapper.offsetWidth;
    wrapper.scrollBy({
        left: direction * scrollAmount,
        behavior: 'smooth',
    });
}