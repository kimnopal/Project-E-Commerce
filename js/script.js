const links = document.querySelectorAll('.nav__links-items');
const toTop = document.querySelector('.scroll');

links.forEach(() => {
    this.addEventListener('click', () => {
        const href = this.getAttribute("href");
        const offSetTop = document.querySelector(href).offsetTop;

        scroll({
            top: offSetTop,
            behavior: "smooth"
        });
    })
})

window.addEventListener('scroll', () => {
    if(window.scrollY > 200) {
        toTop.style.display = "inline-block";
    } else {
        toTop.style.display = "none";
    }
});

toTop.addEventListener('click', () => {
    scroll({
        top: 0,
        behavior: "smooth"
    });
})

const carouselHero = document.querySelectorAll('.jumbotron__img-container');
const jumbotronHero = document.querySelector('.jumbotron__hero-container');
let i = 0;

carouselHero.forEach((carouselItem, index) => {
    carouselItem.style.left = `${index * 100}%`;
});

setInterval(function() {
    carouselHero.forEach(e => {
        e.style.display = 'none';
    });
    
    if(i > 2) {
        i =  0;
    }
    carouselHero[i].style.display = 'block';
    i++;

}, 5000);

setInterval(() => {

    const currentSlide = document.querySelector('.current-slide');
    let nextSlide = currentSlide.nextElementSibling;

    if(nextSlide === null) {
        nextSlide = carouselHero[0];
    }
    const amountToMove = nextSlide.style.left;
    jumbotronHero.style.transform = `translateX(-${amountToMove})`;

    currentSlide.classList.remove('current-slide');
    nextSlide.classList.add('current-slide');

}, 5000);




setInterval(() => {

    if(counter >= carouselImages.length - 1) return;
    if(counter <= 0 ) return;

    // dots[counter - 1].classList.add('current-dot');
    carouselSlide.style.transition = 'transform 0.4s ease-in-out';
    counter++;
    carouselSlide.style.transform = `translateX(-${slideWidth * counter}px)`;
    dots[counter - 2].classList.remove('current-dot');
    dots[counter === carouselImages.length - 1 ? 0 : counter - 1].classList.add('current-dot');

}, 3000);





