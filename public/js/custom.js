// public/js/custom.js
$(document).ready(function () {
    // Initialize the Slick Carousel for images without autoplay
    $('.carousel-slider').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        arrows: true,
        autoplay: false,
    });
});
