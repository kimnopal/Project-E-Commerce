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
    header("Location: index.php");
    exit;
}

// PRODUK
$user_id = $_SESSION["login"]["user_id"];
$products = query("SELECT * FROM cart LEFT JOIN product USING (product_id) WHERE user_id = $user_id");
$username = query("SELECT user_username FROM user WHERE user_id = $user_id")[0]["user_username"];
$cart = count($products);

// CEK APAKAH ADA DATA YANG DIKIRIM
if (!isset($_POST["submit"])) {
    // query semua order sign pada user id terkait yang berstatus belum dibatalkan (selain dibatalkan)
    // (result: [0 => ["order_sign" => .....], 1 => ["order_sign" => ....], ... ])
    $order_signs = query("SELECT order_sign FROM order_db WHERE order_userid = $user_id AND order_status != 'Dibatalkan'");

    // memasukkan semua data order sign kedalam array 1 dimensi (numerik)
    $order_sign = [];
    foreach ($order_signs as $sign) {
        $order_sign[] = $sign["order_sign"];
    }

    // CEK KETERSEDIAAN ID YANG DIKIRIM DAN ADA DIDALAM DB SESUAI DENGAN USER ID LOGIN
    if (!isset($_GET["id"]) || !in_array("#" . $_GET["id"], $order_sign)) {
        header("Location: purchase.php");
    }

    $sign = '#' . $_GET["id"];

    $order_data = query("SELECT * FROM order_db WHERE order_sign = '$sign'")[0];
    $products = query("SELECT * FROM order_product LEFT JOIN product USING (product_id) WHERE order_sign = '$sign'");

    if (isset($_GET["cancel"]) && $order_data["order_status"] == 'Belum Dibayar') {
        mysqli_query($conn, "UPDATE order_db SET
                            order_status = 'Dibatalkan'
                            WHERE order_sign = '$sign'
                            ");
        header("Location: purchase.php");
        exit;
    } elseif (isset($_GET["cancel"]) && $order_data["order_status"] == 'Lunas') {
        echo "
            <script>
                alert('Barang sudah lunas !');
                document.location.href = 'purchase.php';
            </script>
            ";
        exit;
    }

    // DATA PENGIRIMAN
    // dimasukkan kedalam tabel order_db
    $order_sign = $order_data["order_sign"];
    $order_userid = $order_data["order_userid"];
    $order_username = $username;
    $order_name = $order_data["order_name"];
    $order_address = $order_data["order_address"];
    $order_telephone = $order_data["order_telephone"];
    $order_date = $order_data["order_date"];
    $order_totalprice = $order_data["order_totalprice"];
    $order_status = $order_data["order_status"];
} else {
    // DATA PENGIRIMAN
    // dimasukkan kedalam tabel order_db
    $order_sign = uniqid("#");
    $order_userid = $user_id;
    $order_username = $username;
    $order_name = htmlspecialchars($_POST["order_name"]);
    $order_address = htmlspecialchars($_POST["order_address"]);
    $order_telephone = htmlspecialchars($_POST["order_telephone"]);
    date_default_timezone_set("Asia/Jakarta");
    $order_date = date("Y-m-d H:i:s");
    $order_totalprice = 0;
    foreach ($products as $product) {
        $order_totalprice += $product["cart_totalprice"];
    }
    $order_status = "Belum Dibayar";

    mysqli_query($conn, "INSERT INTO order_db 
                        VALUES(null, '$order_sign', $order_userid, '$order_name', '$order_address', '$order_telephone', '$order_date', $order_totalprice, '$order_status')
                        ");

    // DATA BARANG PESANAN
    foreach ($products as $product) {
        $product_id = $product["product_id"];
        $order_product_quantity = $product["cart_quantity"];
        $order_product_price = $product["product_price"];
        $order_product_totalprice = $product["cart_totalprice"];

        mysqli_query($conn, "INSERT INTO order_product 
                            VALUES(null, '$order_sign', $product_id, $order_product_quantity, $order_product_price, $order_product_totalprice)
                            ");
    }
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
    <link rel="stylesheet" href="css/receipt.css">
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

    <!-- ========== RECEIPT ========== -->
    <section class="receipt container">
        <div class="receipt__container">
            <div class="receipt__detail">
                <h2 class="receipt__title">Detail Pemesanan</h2>
                <table class="receipt__info" cellspacing="0">
                    <tr>
                        <th class="receipt__subtitle">ID Pemesanan</th>
                        <td class="receipt__equal">:</td>
                        <td class="receipt__content"><?= $order_sign; ?></td>
                    </tr>
                    <tr>
                        <th class="receipt__subtitle">Username Pemesanan</th>
                        <td class="receipt__equal">:</td>
                        <td class="receipt__content"><?= $order_username; ?></td>
                    </tr>
                    <tr>
                        <th class="receipt__subtitle">Nama Penerima</th>
                        <td class="receipt__equal">:</td>
                        <td class="receipt__content"><?= $order_name; ?></td>
                    </tr>
                    <tr>
                        <th class="receipt__subtitle">Alamat</th>
                        <td class="receipt__equal">:</td>
                        <td class="receipt__content"><?= $order_address; ?></td>
                    </tr>
                    <tr>
                        <th class="receipt__subtitle">Nomor Telepon</th>
                        <td class="receipt__equal">:</td>
                        <td class="receipt__content"><?= $order_telephone; ?></td>
                    </tr>
                    <tr>
                        <th class="receipt__subtitle">Tanggal Pemesanan</th>
                        <td class="receipt__equal">:</td>
                        <td class="receipt__content"><?= $order_date; ?> WIB</td>
                    </tr>
                </table>
            </div>
            <div class="receipt__order">
                <table class="receipt__order-container" cellspacing="0">
                    <tr>
                        <th class="receipt__order-header">Produk</th>
                        <th class="receipt__order-header">Kuantitas</th>
                        <th class="receipt__order-header">Harga Satuan</th>
                        <th class="receipt__order-header">Total Harga</th>
                    </tr>

                    <?php foreach ($products as $product) : ?>
                        <tr class="receipt__order-data-container">
                            <td class="receipt__order-data" data-label="Produk"><?= $product["product_name"]; ?></td>

                            <?php if (isset($_GET["id"])) : ?>
                                <td class="receipt__order-data" data-label="Kuantitas"><?= $product["order_product_quantity"]; ?></td>
                                <td class="receipt__order-data" data-label="Harga Satuan">Rp. <?= number_format($product["order_product_price"], 2, ",", ".");  ?></td>
                                <td class="receipt__order-data" data-label="Total Harga">Rp. <?= number_format($product["order_product_totalprice"], 2, ",", "."); ?></td>
                            <?php else : ?>
                                <td class="receipt__order-data" data-label="Kuantitas"><?= $product["cart_quantity"]; ?></td>
                                <td class="receipt__order-data" data-label="Harga Satuan">Rp. <?= number_format($product["cart_price"], 2, ",", ".");  ?></td>
                                <td class="receipt__order-data" data-label="Total Harga">Rp. <?= number_format($product["cart_totalprice"], 2, ",", "."); ?></td>
                            <?php endif; ?>

                        </tr>
                    <?php endforeach; ?>

                    <tr>
                        <th class="receipt__order-subtotal" colspan="3">Sub Total</th>
                        <th class="receipt__order-price" data-label="Total Harga">Rp. <?= number_format($order_totalprice, 2, ",", "."); ?></th>
                    </tr>
                </table>
            </div>
            <div class="receipt__text">
                <p class="receipt__text-info">Silahkan lakukan pembayaran ke Bank bang tut</p>
                <p class="receipt__text-info">Nomor Account : 0000-0000-0000 (A/N TokoCoba).</p>
                <p class="receipt__text-info">Setelah melakukan pembayaran silahkan lakukan konfirmasi pembayaran</p>
            </div>

            <?php if ($order_status == 'Belum Dibayar') : ?>
                <div class="receipt__button-wrapper">
                    <a href="payment.php?id=<?= isset($_POST["submit"]) ? ltrim($order_sign, "#") : ltrim($_GET["id"], "#"); ?>" class="receipt__confirm receipt__button btn">Konfirmasi Pembayaran</a>

                    <a href="?id=<?= isset($_POST["submit"]) ? ltrim($order_sign, "#") : ltrim($_GET["id"], "#"); ?>&cancel=" onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');" class="receipt__cancel receipt__button btn-red">Batalkan Pemesanan</a>
                </div>
        </div>
    <?php endif; ?>
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