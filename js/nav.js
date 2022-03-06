// NAV TOGGLE
const navToggle = document.querySelector('.nav__toggle');
const navLinks = document.querySelector('.nav__links');

navToggle.addEventListener('click', function() {
    this.classList.toggle('active');
    navLinks.classList.toggle('active');
    if(navToggle.style.position == 'fixed') {
        navToggle.style.position = 'static';
    } else {
        navToggle.style.position = 'fixed';
    }
});


const profileIcon = document.querySelector('.nav__profile');
const menuContainer = document.querySelector('.nav__profile-menu');

profileIcon.addEventListener('click', function() {
    if(menuContainer.style.opacity == '1') {
        menuContainer.style.opacity = '0';
        menuContainer.style.top = '100%';
        menuContainer.style.height = '0';
        return;
    }
    menuContainer.style.height = 'auto';
    menuContainer.style.top = '130%';
    menuContainer.style.opacity = '1';
});

document.addEventListener('click', function(e) {
    const profileContainer = document.querySelector('.nav__profile-container');

    const clickOutside = profileContainer.contains(e.target);
    if(!clickOutside && menuContainer.style.opacity == '1') {
        menuContainer.style.opacity = '0';
        menuContainer.style.top = '100%';
        menuContainer.style.height = '0';
        return;
    }
});