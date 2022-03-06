// toggle responsive
const navToggle = document.querySelector('.nav-toggle');
const container = document.querySelector('.container');

navToggle.addEventListener('click', function() {
    this.classList.toggle('active');
    container.classList.toggle('active');
});


// ajax
// ambil element yang dibutuhkan
const keyword = document.getElementById('keyword');
const containerTable = document.getElementById('main-dashboard');

// tambahkan event ketika keyword ditulis
keyword.addEventListener('keyup', function() {
    // buat object ajax
    const xhr = new XMLHttpRequest();

    // cek kesiapan ajax
    xhr.onreadystatechange = function() {
        if(xhr.readyState == 4 && xhr.status == 200) {
            containerTable.innerHTML = xhr.responseText;
        }
    }

    // eksekusi ajax
    xhr.open('GET', `ajax/ajax.php?keyword=${keyword.value}`, true);
    xhr.send();
});
