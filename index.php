<?php
session_start();
require 'functions.php';

$login = isset($_SESSION["login"]);

// RESET SESSION CARI
unset($_SESSION["search"]);
unset($_SESSION["filter"]);
unset($_SESSION["category_id"]);
unset($_SESSION["product_price_1"]);
unset($_SESSION["product_price_2"]);
unset($_SESSION["product_status"]);
unset($_SESSION["order"]);

// MENAMBAHKAN SESSION CART
// cek apakah tombol add ditekan
// $_GET["buy"] berisi id produk
if (isset($_GET["buy"])) {
    // cek apakah session produk sudah dipesan sebelumnya
    if (isset($_SESSION["cart"][$_GET["buy"]])) {
        // tambahkan jumlahnya
        $_SESSION["cart"][$_GET["buy"]]++;
        header("Location: everywhere.php");
        exit;
    } else {
        // masukkan produk ke session cart
        $_SESSION["cart"][$_GET["buy"]] = 1;
        header("Location: everywhere.php");
        exit;
    }
}

// MEMASUKKAN DATA KE DB & HITUNG ISI CART
if ($login) {
    $user_id = $_SESSION["login"]["user_id"];
    $user_cart = query("SELECT * FROM cart WHERE user_id = $user_id");

    if (empty($user_cart)) {
        if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
            AddCartToDB($_SESSION["cart"]);
        }
    } else {
        if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
            AddCartToDBFiltered($_SESSION["cart"]);
        }
    }

    $user_cart = query("SELECT * FROM cart WHERE user_id = $user_id");
    $cart = count($user_cart);
} else {
    $cart = isset($_SESSION["cart"]) ? count($_SESSION["cart"]) : 0;
}

// KATEGORI
$categories = query("SELECT * FROM category");

// PRODUK
$products = query("SELECT * FROM product LEFT JOIN category USING (category_id) LIMIT 12");

// JUMBOTRON
$jumbotrons = query("SELECT * FROM jumbotron");
$asides = query("SELECT * FROM aside");
$abouts = query("SELECT * FROM about");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Coba</title>
    <!-- ICON -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/stye.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/scroll.css">
    <link rel="stylesheet" href="css/jumbotron.css">
    <link rel="stylesheet" href="css/category.css">
    <link rel="stylesheet" href="css/product.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="css/contact.css">
    <link rel="stylesheet" href="css/socialmedia.css">
    <link rel="stylesheet" href="css/footer.css">
</head>

<body>

    <!-- ========== HEADER ========== -->
    <header class="container mb--sm--50">
        <!-- ========== NAVBAR ========== -->
        <nav class="nav">
            <div class="nav__toggle">
                <span class="nav__toggle-hamburger"></span>
                <span class="nav__toggle-hamburger"></span>
                <span class="nav__toggle-hamburger"></span>
            </div>
            <div class="nav__brand">
                <a href="" class="nav__brand-name">Toko<span class="text--bold text--blue--primary">Coba</span></a>
            </div>
            <ul class="nav__links">
                <li><a href="" class="nav__links-items">Home</a></li>
                <li><a href="#product" class="nav__links-items">Product</a></li>
                <li><a href="#about" class="nav__links-items">About</a></li>
                <li><a href="#contact" class="nav__links-items">Contact</a></li>
            </ul>
            <div class="nav__field">
                <form action="search.php" method="post" class="nav__search">
                    <input type="text" name="keyword_search" id="search" class="nav__search-field" placeholder="Search">
                    <button type="submit" name="search" class="nav__submit-field"><i class="fas fa-search"></i></button>
                </form>
                <div class="nav__profile-container">
                    <div class="nav__profile"><i class="fas fa-user"></i></div>
                    <div class="nav__profile-menu">
                        <?php if ($login) : ?>
                            <a href="login/logout.php" class="nav__profile-menu-item nav__logout">Logout</a>
                        <?php else : ?>
                            <a href="login/login.php" class="nav__profile-menu-item nav__login">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="nav__shopping-cart">
                    <a href="cart.php" class="nav__shopping-cart-icon"><i class="fas fa-shopping-cart"></i></a>
                    <a href="cart.php" class="nav__shopping-cart-notif"><?= $cart; ?></a>
                </div>
            </div>
        </nav>
    </header>

    <a href="#" class="scroll">
        <i class="fas fa-chevron-up arrow"></i>
    </a>

    <!-- ========== JUMBOTRON ========== -->
    <section class="jumbotron container pt--0">
        <div class="jumbotron__container">
            <div class="jumbotron__carousel">
                <span class="jumbotron__carousel-prev-btn btn--prev"><i class="fas fa-chevron-left arrow"></i></span>
                <div class="jumbotron__carousel-slide">
                    <?php foreach ($jumbotrons as $jumbotron) : ?>
                        <?php if (array_search($jumbotron, $jumbotrons) == 0) : ?>
                            <a href="" class="jumbotron__carousel-img-container" id="last-clone">
                                <img src="dashboard/tampilan/img/<?= end($jumbotrons)["jumbotron_image"]; ?>" alt="" class="jumbotron__carousel-img">
                            </a>
                        <?php endif; ?>

                        <a href="<?= $jumbotron["jumbotron_link"]; ?>" class="jumbotron__carousel-img-container">
                            <img src="dashboard/tampilan/img/<?= $jumbotron["jumbotron_image"]; ?>" alt="" class="jumbotron__carousel-img">
                        </a>

                        <?php if (array_search($jumbotron, $jumbotrons) == count($jumbotrons) - 1) : ?>
                            <a href="" class="jumbotron__carousel-img-container" id="first-clone">
                                <img src="dashboard/tampilan/img/<?= $jumbotrons[0]["jumbotron_image"]; ?>" alt="" class="jumbotron__carousel-img">
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <span class="jumbotron__carousel-next-btn btn--next"><i class="fas fa-chevron-right arrow"></i></span>
                <div class="jumbotron__carousel-dots">
                    <?php foreach ($jumbotrons as $jumbotron) : ?>
                        <?php if (array_search($jumbotron, $jumbotrons) == 0) : ?>
                            <span class="jumbotron__carousel-dot current-dot"></span>
                        <?php continue;
                        endif; ?>
                        <span class="jumbotron__carousel-dot"></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="jumbotron__aside-top">
                <a href="<?= $asides[0]["aside_link"]; ?>" class="jumbotron__aside-container">
                    <img src="dashboard/tampilan/img/<?= $asides[0]["aside_image"]; ?>" class="jumbotron__aside-img" alt="">
                </a>
            </div>
            <div class="jumbotron__aside-bottom">
                <a href="<?= $asides[1]["aside_link"]; ?>" class="jumbotron__aside-container">
                    <img src="dashboard/tampilan/img/<?= $asides[1]["aside_image"]; ?>" class="jumbotron__aside-img" alt="">
                </a>
            </div>
        </div>
    </section>

    <!-- ========== KATEGORI NAV ========== -->
    <section class="category container" id="product">
        <div class="category__carousel">
            <div class="category__carousel-prev-btn btn--prev"><i class="fas fa-chevron-left arrow"></i></div>
            <div class="category__carousel-slide">
                <div class="category__icon-container">
                    <div class="category__icon-inner-container active" id="">
                        <i class="fas fa-list category__icon"></i>
                        <p class="category__caption">Semua</p>
                    </div>
                </div>
                <?php foreach ($categories as $category) : ?>
                    <?php if ($category["category_status"] === "Tidak Aktif") {
                        continue;
                    } ?>
                    <div class="category__icon-container">
                        <div class="category__icon-inner-container" id="<?= $category['category_id']; ?>">
                            <i class="<?= $category["category_icon"]; ?> category__icon"></i>
                            <p class="category__caption"><?= $category["category_name"]; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="category__carousel-next-btn btn--next"><i class="fas fa-chevron-right arrow"></i></div>
        </div>
    </section>

    <!-- ========== Produk ========== -->
    <section class="product container">
        <div class="product__container">
            <?php foreach ($products as $product) : ?>
                <?php if ($product["product_status"] == 0) {
                    continue;
                } ?>
                <div class="product__card card">
                    <div class="product__card-image card__image-container"><img src="dashboard/produk/img/<?= $product["product_image"]; ?>" class="card__image" alt=""></div>
                    <div class="card__text">
                        <a href="?buy=<?= $product["product_id"]; ?>" class="card__add-cart">+</a>
                        <a href="search.php?id=<?= $product["category_id"]; ?>" class="card__category"><?= $product["category_name"]; ?></a>
                        <a href="detail.php?id=<?= $product["product_id"]; ?>" class="card__title"><?= $product["product_name"]; ?></a>
                        <a href="detail.php?id=<?= $product["product_id"]; ?>" class="card__price-container">
                            <div class="card__disc" style="display: <?= !empty($product["product_discount"]) ? "initial" : "none"; ?>;">
                                <?= $product["product_discount"] ? "Rp. " . number_format($product["product_discount"], 2, ",", ".") : ""; ?>
                            </div>
                            <div class="card__price">
                                Rp. <?= number_format($product["product_price"], 2, ",", "."); ?>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($products) != 0) : ?>
            <div class="product__link-more-container"><a href="search.php?category_id=" class="product__link-more">Lihat semua</a></div>
        <?php else : ?>
            <div class="product__empty">Produk Kosong</div>
        <?php endif; ?>
    </section>

    <!-- ========== ABOUT ========== -->
    <section class="about container" id="about">
        <div class="about__container">
            <div class="about__image-container">
                <div class="about__image-left">
                    <img src="dashboard/tampilan/img/<?= $abouts[0]["about_image"]; ?>" alt="" class="about__image">
                </div>
                <div class="about__image-right-top">
                    <img src="dashboard/tampilan/img/<?= $abouts[1]["about_image"]; ?>" alt="" class="about__image">
                </div>
                <div class="about__image-right-bottom">
                    <img src="dashboard/tampilan/img/<?= $abouts[2]["about_image"]; ?>" alt="" class="about__image">
                </div>
            </div>
            <div class="about__text-container">
                <h2 class="about__title">Toko<span class="text--bold text--blue--primary">Coba</span></h2>
                <p class="about__desc">Sudah berdiri sejak tahun <strong class="text--blue--primary">2003</strong> dan memiliki lebih dari <strong class="text--blue--primary">10.000</strong> users.</p>
                <p class="about__desc">Semua produk memiliki kualitas yang sangat bagus dan <strong class="text--blue--primary">100% original.</strong></p>
            </div>
        </div>
    </section>

    <!-- ========== CONTACT ========== -->
    <section class="contact container" id="contact">
        <div class="contact__container">
            <div class="contact__info">
                <h2 class="contact__info-title">Informasi Lebih Lanjut</h2>
                <div class="contact__info-desc">
                    <div class="contact__info-field">
                        <div class="contact__icon-container">
                            <i class="fas fa-map-marker-alt contact__icon"></i>
                        </div>
                        <div class="contact__info-text">
                            <h3 class="contact__info-text-title">Alamat</h3>
                            <p class="contact__info-text-desc">Indonesia</p>
                        </div>
                    </div>
                    <div class="contact__info-field">
                        <div class="contact__icon-container">
                            <i class="fas fa-phone contact__icon"></i>
                        </div>
                        <div class="contact__info-text">
                            <h3 class="contact__info-text-title">Telp</h3>
                            <p class="contact__info-text-desc">+62 345 6789 0123</p>
                        </div>
                    </div>
                    <div class="contact__info-field">
                        <div class="contact__icon-container">
                            <i class="fas fa-envelope contact__icon"></i>
                        </div>
                        <div class="contact__info-text">
                            <h3 class="contact__info-text-title">Email</h3>
                            <p class="contact__info-text-desc">tokocoba@gmail.com</p>
                        </div>
                    </div>
                </div>
            </div>

            <form action="" class="contact__form">
                <div class="contact__form-input-field">
                    <label for="nama" class="contact__form-label">Nama</label>
                    <input type="text" name="nama" id="nama" class="contact__form-input">
                </div>
                <div class="contact__form-input-field">
                    <label for="email" class="contact__form-label">Email</label>
                    <input type="email" name="email" id="email" class="contact__form-input">
                </div>
                <div class="contact__form-input-field">
                    <label for="nama" class="contact__form-label">Nama</label>
                    <textarea name="nama" id="nama" class="contact__form-input" cols="30" rows="10"></textarea>
                </div>
                <button type="submit" name="submit" class="contact__form-submit">kirim</button>
            </form>
        </div>
    </section>

    <!-- ========== SOCIAL MEDIA ========== -->
    <section class="social container">
        <div class="social__container">
            <a href="" class="social__icon-container"><i class="social__icon fab fa-instagram"></i></a>
            <a href="" class="social__icon-container"><i class="social__icon fab fa-facebook-f"></i></a>
            <a href="" class="social__icon-container"><i class="social__icon fab fa-whatsapp"></i></a>
            <a href="" class="social__icon-container"><i class="social__icon fab fa-telegram-plane"></i></a>
            <a href="" class="social__icon-container"><i class="social__icon fab fa-twitter"></i></a>
            <a href="" class="social__icon-container"><i class="social__icon fab fa-twitter"></i></a>
        </div>
    </section>

    <!-- ========== FOOTER ========== -->
    <footer class="footer container">
        <div class="footer__container">
            <p class="footer__text">&copy; 2022 TokoCoba | Built by <a href="" class="footer__link">Naufal Hakim</a></p>
        </div>
    </footer>

    <!-- ========== CUSTOM JS ========== -->
    <script src="js/script.js"></script>
    <script src="js/nav.js"></script>
    <script src="js/jumbotron.js"></script>
    <script src="js/category.js"></script>
</body>

</html>