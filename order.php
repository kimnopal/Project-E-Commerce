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

// CEK SESSION LOGIN
$login = isset($_SESSION["login"]["user"]);
if (!$login) {
    $_SESSION["order"] = true;
    header("Location: login/login.php");
    exit;
}

unset($_SESSION["order"]);

$user_id = $_SESSION["login"]["user_id"];
$products = query("SELECT * FROM cart WHERE user_id = $user_id");

// CEK APAKAH CART USER KOSONG ATAU TIDAK (DB)
if (empty($products)) {
    if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
        AddCartToDB($_SESSION["cart"]);
    } else {
        header("Location: index.php");
        exit;
    }
} else {
    if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
        AddCartToDBFiltered(($_SESSION["cart"]));
    }
}

// CEK PRODUK YANG BELUM DIBAYAR
$order_total = query("SELECT order_sign FROM order_db WHERE order_userid = $user_id AND order_status = 'Belum Dibayar'");
if (count($order_total) == 3) {
    echo "<script>
            alert('Tidak bisa melakukan pemesanan. Anda masih memiliki 3 pesanan yang belum dibayar !.');
            document.location.href = 'cart.php';
        </script>";
}


// PRODUK
$products = query("SELECT * FROM cart LEFT JOIN product USING (product_id) WHERE user_id = $user_id");

// HITUNG ISI CART
$cart = count($products);

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
    <link rel="stylesheet" href="css/order.css">
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

    <!-- ========== ORDER ========== -->
    <section class="order container">
        <div class="order__container">
            <div class="order__delivery">
                <h2 class="order__delivery-title">Data Pengiriman</h2>
                <form action="receipt.php" method="post" class="order__delivery-input-container">
                    <div class="order__delivery-input-field">
                        <label for="order_name" class="order__delivery-input-title">Nama Penerima</label>
                        <input type="text" name="order_name" id="order_name" class="order__delivery-input" required>
                    </div>
                    <div class="order__delivery-input-field">
                        <label for="order_telephone" class="order__delivery-input-title">Nomor Telepon</label>
                        <input type="text" name="order_telephone" id="order_telephone" class="order__delivery-input" required>
                    </div>
                    <div class="order__delivery-input-field">
                        <label for="order_address" class="order__delivery-input-title">Alamat Penerima</label>
                        <textarea name="order_address" id="order_address" class="order__delivery-input" cols="30" rows="10" required></textarea>
                    </div>
                    <button type="submit" name="submit" class="order__delivery-submit">Submit</button>
                </form>
            </div>
            <div class="order__detail">
                <h2 class="order__detail-title">Detail Pemesanan</h2>
                <table class="order__detail-container" cellspacing="0">
                    <tr>
                        <th class="order__detail-header">Produk</th>
                        <th class="order__detail-header">Kuantitas</th>
                        <th class="order__detail-header">Total Harga</th>
                    </tr>

                    <?php $subtotal = 0; ?>
                    <?php foreach ($products as $product) : ?>
                        <?php $subtotal += $product["cart_totalprice"]; ?>
                        <tr class="order__detail-data-container">
                            <td class="order__detail-data" data-label="Produk"><?= $product["product_name"]; ?></td>
                            <td class="order__detail-data" data-label="Kuantitas"><?= $product["cart_quantity"]; ?></td>
                            <td class="order__detail-data" data-label="Total Harga"><?= number_format($product["cart_totalprice"], 2, ",", "."); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <tr>
                        <th class="order__detail-subtotal" colspan="2">Subtotal</th>
                        <th class="order__detail-price" data-label="Subtotal">Rp. <?= number_format($subtotal, 2, ",", "."); ?></th>
                    </tr>
                </table>
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