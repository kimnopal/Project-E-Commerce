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

// PURCHASE
$purchases = query("SELECT * FROM order_db WHERE order_userid = $user_id AND order_status != 'Dibatalkan' ORDER BY order_date DESC");

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
    <link rel="stylesheet" href="css/purchase.css">
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

    <!-- ========== PURCHASE ========== -->
    <section class="purchase container">
        <div class="purchase__container">
            <table class="purchase__table" cellspacing="0">
                <?php if (!empty($purchases)) : ?>
                    <tr>
                        <th class="purchase__header">ID Pemesanan</th>
                        <th class="purchase__header">Tanggal Pesanan</th>
                        <th class="purchase__header">Total Pembayaran</th>
                        <th class="purchase__header">Status Pembayaran</th>
                        <th class="purchase__header">Aksi</th>
                    </tr>

                    <?php foreach ($purchases as $purchase) : ?>
                        <?php if ($purchase["order_status"] === "Belum Dibayar") : ?>
                            <tr class="purchase__data-container">
                                <td class="purchase__data" data-label="ID Pemesanan"><?= $purchase["order_sign"]; ?></td>
                                <td class="purchase__data" data-label="Tanggal Pesanan"><?= $purchase["order_date"]; ?></td>
                                <td class="purchase__data" data-label="Total Pembayaran">Rp. <?= number_format($purchase["order_totalprice"], 2, ",", "."); ?></td>
                                <td class="purchase__data" data-label="Status Pembayaran"><?= $purchase["order_status"]; ?></td>
                                <td class="purchase__data" data-label="Aksi">
                                    <a href="receipt.php?id=<?= ltrim($purchase["order_sign"], "#"); ?>" class="purchase__button">Detail</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php foreach ($purchases as $purchase) : ?>
                        <?php if ($purchase["order_status"] === "Proses Verifikasi") : ?>
                            <tr class="purchase__data-container">
                                <td class="purchase__data" data-label="ID Pemesanan"><?= $purchase["order_sign"]; ?></td>
                                <td class="purchase__data" data-label="Tanggal Pesanan"><?= $purchase["order_date"]; ?></td>
                                <td class="purchase__data" data-label="Total Pembayaran">Rp. <?= number_format($purchase["order_totalprice"], 2, ",", "."); ?></td>
                                <td class="purchase__data" data-label="Status Pembayaran"><?= $purchase["order_status"]; ?></td>
                                <td class="purchase__data" data-label="Aksi">
                                    <a href="receipt.php?id=<?= ltrim($purchase["order_sign"], "#"); ?>" class="purchase__button">Detail</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php foreach ($purchases as $purchase) : ?>
                        <?php if ($purchase["order_status"] === "Lunas") : ?>
                            <tr class="purchase__data-container">
                                <td class="purchase__data" data-label="ID Pemesanan"><?= $purchase["order_sign"]; ?></td>
                                <td class="purchase__data" data-label="Tanggal Pesanan"><?= $purchase["order_date"]; ?></td>
                                <td class="purchase__data" data-label="Total Pembayaran">Rp. <?= number_format($purchase["order_totalprice"], 2, ",", "."); ?></td>
                                <td class="purchase__data" data-label="Status Pembayaran"><?= $purchase["order_status"]; ?></td>
                                <td class="purchase__data" data-label="Aksi">
                                    <a href="receipt.php?id=<?= ltrim($purchase["order_sign"], "#"); ?>" class="purchase__button">Detail</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>

                <?php else : ?>
                    <tr>
                        <td colspan="5" class="purchase__empty">Belum Ada Transaksi Apapun</td>
                    </tr>
                <?php endif; ?>
            </table>

            <a href="cart.php" class=" purchase__back purchase__button btn">
                < Kembali</a>
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