// CAROUSEL JUMBOTRON
const carouselSlide = document.querySelector('.jumbotron__carousel-slide');
const carouselImages = document.querySelectorAll('.jumbotron__carousel-img-container');

// btn
const prevBtn = document.querySelector('.jumbotron__carousel-prev-btn');
const nextBtn = document.querySelector('.jumbotron__carousel-next-btn');
const dots = document.querySelectorAll('.jumbotron__carousel-dot');

// counter and width
let counter = 1;
let slideWidth = carouselImages[0].clientWidth;


carouselSlide.style.transform = `translateX(-${slideWidth * counter}px)`;

// dots event
dots.forEach((dot, index) => {
    dot.addEventListener('click', function() {
        counter = index + 1;
        carouselSlide.style.transition = 'transform 0.4s ease-in-out';
        carouselSlide.style.transform = `translateX(-${slideWidth * counter}px)`;
        
        if(dot.classList.contains('current-dot')) {
            return;
        } else {
            // remove class elemen sebelum
            dots[index <= 0 ? dots.length - 1 : index - 1].classList.remove('current-dot');
            // remove class elemen sesudah
            dots[index >= dots.length - 1 ? 0 : index + 1].classList.remove('current-dot');
            // add class elemen current
            dots[index].classList.add('current-dot');
        }
    });
});

function refreshScreenWidth() {
    slideWidth = carouselImages[0].clientWidth;
    
    carouselSlide.style.transform = `translateX(-${slideWidth * counter}px)`;
}

window.addEventListener('resize', refreshScreenWidth);

nextBtn.addEventListener('click', () => {
    if(counter >= carouselImages.length - 1) return;
    carouselSlide.style.transition = 'transform 0.4s ease-in-out';
    counter++;
    carouselSlide.style.transform = `translateX(-${slideWidth * counter}px)`;

    dots[counter - 2].classList.remove('current-dot');
    dots[counter === carouselImages.length - 1 ? 0 : counter - 1].classList.add('current-dot');
});

prevBtn.addEventListener('click', () => {
    if(counter <= 0 ) return;
    carouselSlide.style.transition = 'transform 0.4s ease-in-out';
    counter--;
    carouselSlide.style.transform = `translateX(-${slideWidth * counter}px)`;

    dots[counter].classList.remove('current-dot');
    dots[counter === 0 ? 2 : counter - 1].classList.add('current-dot');
});

function checkClone() {
    if(carouselImages[counter].id === 'last-clone') {
        carouselSlide.style.transition = 'none';
        counter = carouselImages.length - 2;
        carouselSlide.style.transform = `translateX(-${slideWidth * counter}px)`;
    }

    if(carouselImages[counter].id === 'first-clone') {
        carouselSlide.style.transition = 'none';
        counter = carouselImages.length - counter;
        carouselSlide.style.transform = `translateX(-${slideWidth * counter}px)`;
    }
}

carouselSlide.addEventListener('transitionend', checkClone);