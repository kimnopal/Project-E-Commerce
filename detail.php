<?php
session_start();
require 'functions.php';

// MENANGKAP DATA
$login = isset($_SESSION["login"]);
$product_id = $_GET["id"];

// JIKA ID TIDAK ADA
if (empty($product_id)) {
    header("Location: index.php");
}

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
// $_GET["add"] berisi id produk
if (isset($_GET["add"]) || isset($_GET["buy"])) {
    // cek apakah session produk sudah dipesan sebelumnya
    if (isset($_SESSION["cart"][$_GET["add"]]) || isset($_SESSION["cart"][$_GET["buy"]])) {
        // tambahkan jumlahnya
        if (isset($_GET["add"])) {
            $_SESSION["cart"][$_GET["add"]]++;
            header("Location: everywhere.php?id=$product_id&key=detail");
            exit;
        }

        $_SESSION["cart"][$_GET["buy"]]++;
        header("Location: cart.php");
        exit;
    } else {
        // masukkan produk ke session cart
        if (isset($_GET["add"])) {
            $_SESSION["cart"][$_GET["add"]] = 1;
            header("Location: everywhere.php?id=$product_id&key=detail");
            exit;
        }

        $_SESSION["cart"][$_GET["buy"]] = 1;
        header("Location: cart.php");
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
$product = query("SELECT * FROM product LEFT JOIN category USING (category_id) WHERE product_id = $product_id")[0];
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
    <link rel="stylesheet" href="css/detail.css">
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
                <a href="index.php" class="nav__brand-name">Toko<span class="text--bold text--blue--primary">Coba</span></a>
            </div>
            <ul class="nav__links">
                <li><a href="index.php" class="nav__links-items">Home</a></li>
                <li><a href="index.php#product" class="nav__links-items">Product</a></li>
                <li><a href="index.php#about" class="nav__links-items">About</a></li>
                <li><a href="index.php#contact" class="nav__links-items">Contact</a></li>
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
                    <a href="cart.php" class="nav__shopping-cart-notif"><?= isset($cart) ? $cart : "0"; ?></a>
                </div>
            </div>
        </nav>
    </header>

    <!-- ========== DETAIL ========== -->
    <section class="detail container">
        <div class="detail__container">
            <div class="detail__image-wrapper">
                <img src="dashboard/produk/img/<?= $product["product_image"]; ?>" alt="" class="detail__image">
            </div>
            <div class="detail__info">
                <div class="detail__info-header">
                    <h2 class="detail__info-title"><?= $product["product_name"]; ?></h2>
                    <a href="" class="detail__info-subtitle"><?= $product["category_name"]; ?></a>
                </div>
                <div class="detail__price-wrapper">
                    <p class="detail__discount" style="display: <?= !empty($product["product_discount"]) ? "initial" : "none"; ?>;">
                        <?= $product["product_discount"] ? "Rp. " . number_format($product["product_discount"], 2, ",", ".") : ""; ?>
                    </p>
                    <h3 class="detail__price">
                        Rp. <?= number_format($product["product_price"], 2, ",", "."); ?>
                    </h3>
                </div>
                <p class="detail__description">
                    <?= $product["product_description"]; ?>
                </p>
                <div class="detail__button-wrapper">
                    <a href="?id=<?= $product_id; ?>&add=<?= $product_id; ?>" class="detail__add-cart detail__button"><span>+</span>Tambah Ke Keranjang</a>
                    <a href="?id=<?= $product_id; ?>&buy=<?= $product_id; ?>" class="detail__add detail__button"><span><i class="fas fa-shopping-cart"></i></span>Beli Sekarang</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== FOOTER ========== -->
    <footer class="footer container">
        <div class="footer__container">
            <p class="footer__text">&copy; 2022 TokoCoba | Built by <a href="" class="footer__link">Naufal Hakim</a></p>
        </div>
    </footer>

    <!-- ========== CUSTOM JS ========== -->
    <script src="js/nav.js"></script>
</body>

</html>