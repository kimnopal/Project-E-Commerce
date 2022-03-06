// CAROUSEL CATEGORY
const categorySlide = document.querySelector('.category__carousel-slide');

// icon
let showedIcon = 6;
let countIcon = categorySlide.childElementCount - showedIcon;

// width
let categoryCounter = 0;
let categorySlideWidth = (categorySlide.clientWidth) / showedIcon;

// button
const categoryPrevBtn = document.querySelector('.category__carousel-prev-btn');
const categoryNextBtn = document.querySelector('.category__carousel-next-btn');

// media query
function mediaQuery() {
    if(this.innerWidth <= 375) {
        showedIcon = 3
    } else if(this.innerWidth <= 576) {
        showedIcon = 4;
    } else if(this.innerWidth <= 960) {
        showedIcon = 5;
    } else {
        showedIcon = 6;
    }
    countIcon = categorySlide.childElementCount - showedIcon;
    categorySlideWidth = (categorySlide.clientWidth - 30) / showedIcon;
    btnHider(categoryCounter, countIcon);
    categorySlide.style.transform = `translateX(-${categoryCounter * categorySlideWidth}px)`;
}

window.addEventListener('resize', mediaQuery);

mediaQuery();

function btnHider(counter, jumlahIconOverflow) {
    if(jumlahIconOverflow <= 0 ) {
        categoryPrevBtn.style.display = 'none';
        categoryNextBtn.style.display = 'none';
        return;
    }
    
    if(counter <= 0) {
        categoryPrevBtn.style.display = 'none';
    } else {
        categoryPrevBtn.style.display = 'initial';
    }

    if(counter >= jumlahIconOverflow) {
        categoryNextBtn.style.display = 'none';
    } else {
        categoryNextBtn.style.display = 'initial';
    }
}

btnHider(categoryCounter, countIcon);


categoryNextBtn.addEventListener('click', function() {
    categoryCounter++;
    btnHider(categoryCounter, countIcon);
    categorySlide.style.transform = `translateX(-${categoryCounter * categorySlideWidth}px)`;
});

categoryPrevBtn.addEventListener('click', function() {
    categoryCounter--;
    btnHider(categoryCounter, countIcon);
    categorySlide.style.transform = `translateX(-${categoryCounter * categorySlideWidth}px)`;
});


// category slide
const categoryIcon = document.querySelectorAll('.category__icon-inner-container');
categoryIcon.forEach((icon) => {
    icon.addEventListener('click', function() {
        categoryIcon.forEach((e) => {
            e.classList.remove('active');
        })
        this.classList.add('active');

        // AJAX
        // elemen yang diperlukan
        const iconId = this.id;
        const productContainer = document.querySelector('.product');

        // buat object ajax
        const xhr = new XMLHttpRequest();

        // cek kesiapan ajax
        xhr.onreadystatechange = function() {
            if(xhr.readyState == 4 && xhr.status == 200) {
                productContainer.innerHTML = xhr.responseText;
            }
        }

        // eksekusi ajax
        xhr.open('GET', `ajax/ajax.php?category_id=${iconId}`, true);
        xhr.send();
    });
})