<?php
session_start();
require 'functions.php';

// RESET SESSION
unset($_SESSION["search"]);
unset($_SESSION["filter"]);
unset($_SESSION["category_id"]);
unset($_SESSION["product_price_1"]);
unset($_SESSION["product_price_2"]);
unset($_SESSION["product_status"]);
unset($_SESSION["order"]);

// CEK SESSION LOGIN
$login = isset($_SESSION["login"]["user"]);
if (!$login) {
    header("Location: index.php");
    exit;
}

// CART
$user_id = $_SESSION["login"]["user_id"];
$products = query("SELECT * FROM cart WHERE user_id = $user_id");

// HITUNG ISI CART
$cart = count($products);

// query semua order sign pada user id terkait
$order_signs = query("SELECT order_sign FROM order_db WHERE order_userid = $user_id AND order_status = 'Belum Dibayar'");

// memasukkan semua data order sign kedalam array 1 dimensi (numerik)
$order_sign = [];
foreach ($order_signs as $sign) {
    $order_sign[] = $sign["order_sign"];
}

// CEK KETERSEDIAAN ID YANG DIKIRIM DAN ADA DIDALAM DB SESUAI DENGAN USER ID LOGIN
if (!isset($_GET["id"]) || !in_array("#" . $_GET["id"], $order_sign)) {
    header("Location: purchase.php");
    exit;
}

if (isset($_POST["submit"])) {
    $order_sign = "#" . $_GET["id"];
    date_default_timezone_set("Asia/Jakarta");
    $payment_date = date("Y-m-d H:i:s");
    $payment_account = htmlspecialchars($_POST["payment_account"]);
    $payment_account_name = htmlspecialchars($_POST["payment_account_name"]);

    mysqli_query($conn, "INSERT INTO payment 
                        VALUES(null, '$order_sign', '$payment_date', '$payment_account', '$payment_account_name')
                        ");

    mysqli_query($conn, "UPDATE order_db SET
                        order_status = 'Proses Verifikasi'
                        WHERE order_sign = '$order_sign'
                        ");
    header("Location: purchase.php");
    exit;
}

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
    <link rel="stylesheet" href="css/payment.css">
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
                    <a href="cart.php" class="nav__shopping-cart-notif"><?= $cart; ?></a>
                </div>
            </div>
        </nav>
    </header>

    <!-- ========== PAYMENT ========== -->
    <section class="payment container">
        <div class="payment__container">
            <form action="?id=<?= $_GET["id"]; ?>" method="post" class="payment_wrapper">
                <div class="payment__input-field">
                    <label for="payment_account" class="payment__input-title">Nomor Rekening</label>
                    <input type="text" name="payment_account" id="payment_account" class="payment__input">
                </div>
                <div class="payment__input-field">
                    <label for="payment_account_name" class="payment__input-title">Nama Rekening (A/N)</label>
                    <input type="text" name="payment_account_name" id="payment_account_name" class="payment__input">
                </div>
                <button type="submit" name="submit" class="payment__button">Konfirmasi</button>
            </form>
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