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

// KATEGORI
$categories = query("SELECT * FROM category");

// DELETE BARANG DI CART
if (isset($_GET["del"])) {
    $product_id = $_GET["del"];
    if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
        unset($_SESSION["cart"][$product_id]);
    } else {
        mysqli_query($conn, "DELETE FROM cart WHERE product_id = $product_id");
    }
    header("Location: everywhere.php?key=quantity");
}

// PRODUK
$login = isset($_SESSION["login"]["user"]);

// jika belum login
if (!$login) {
    // QUERY PRODUK PADA SESSION CART
    if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
        quantity();

        // mengambil key (id) dari array session cart
        $array_keys = array_keys($_SESSION["cart"]);

        // melakukan perulangan query data dari id barang yang ada di cart
        foreach ($array_keys as $product_id) {
            // assign hasil query kedalam array
            $products[] = query("SELECT * FROM product WHERE product_id = $product_id")[0];
        }
    } else {
        $products = [];
    }
} else { // jika sudah login
    $user_id = $_SESSION["login"]["user_id"];
    $products = query("SELECT * FROM cart WHERE user_id = $user_id");

    if (empty($products)) {
        if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
            addCartToDB($_SESSION["cart"]);
            $products = query("SELECT * FROM cart LEFT JOIN product USING (product_id) WHERE user_id = $user_id");
        } else {
            $products = [];
        }
    } else {
        if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
            AddCartToDBFiltered($_SESSION["cart"]);
            $products = query("SELECT * FROM cart LEFT JOIN product USING (product_id) WHERE user_id = $user_id");
        } else {
            quantity();
            $products = query("SELECT * FROM cart LEFT JOIN product USING (product_id) WHERE user_id = $user_id");
        }
    }
}

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
    <link rel="stylesheet" href="css/cart.css">
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
                    <a href="" class="nav__shopping-cart-icon"><i class="fas fa-shopping-cart"></i></a>
                    <a href="" class="nav__shopping-cart-notif"><?= isset($cart) ? $cart : "0"; ?></a>
                </div>
            </div>
        </nav>
    </header>

    <!-- ========== CART ========== -->
    <section class="cart container">
        <div class="cart__container">

            <?php if ($login) : ?>
                <div class="cart__order">
                    <a href="purchase.php" class="cart__order-wrapper btn">
                        <i class="fas fa-money-check cart__order-icon"></i>
                        <div class="cart__order-nav">Pesanan</div>
                    </a>
                </div>
            <?php endif; ?>
            <table class="cart__table" cellspacing="0">
                <?php if (!empty($products)) : ?>
                    <tr class="cart__row">
                        <th class="cart__header">No.</th>
                        <th class="cart__header">Produk</th>
                        <th class="cart__header">Harga</th>
                        <th class="cart__header">Kuantitas</th>
                        <th class="cart__header">Total Harga</th>
                        <th class="cart__header">Aksi</th>
                    </tr>

                    <?php
                    $no = 1;
                    $subtotal = 0;
                    ?>
                    <?php foreach ($products as $product) : ?>
                        <?php
                        if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"]) && !$login) {
                            $product_quantity = in_array($product["product_id"], $array_keys) ? $_SESSION["cart"][$product["product_id"]] : 0;
                            $total_price = $product_quantity * $product["product_price"];
                            $subtotal += $total_price;
                        } else {
                            $product_quantity = $product["cart_quantity"];
                            $total_price = $product["cart_totalprice"];
                            $subtotal += $total_price;
                        }
                        ?>

                        <tr class="cart__data-container">
                            <td class="cart__data cart__number" data-label="No."><?= $no; ?></td>
                            <td class="cart__data" data-label="Produk">
                                <div class="cart__product-wrapper">
                                    <img src="dashboard/produk/img/<?= $product["product_image"]; ?>" alt="" class="cart__product-img">
                                    <h3 class="cart__product-title"><?= $product["product_name"]; ?></h3>
                                </div>
                            </td>
                            <td class="cart__data" data-label="Harga">
                                <div class="cart__price-wrapper">
                                    <p class="cart__discount" style="display: <?= !empty($product["product_discount"]) ? "initial" : "initial"; ?>;">
                                        <?= $product["product_discount"] ? "Rp. " . number_format($product["product_discount"], 2, ",", ".") : ""; ?>
                                    </p>
                                    <h3 class="cart__price">
                                        Rp. <?= number_format($product["product_price"], 2, ",", "."); ?>
                                    </h3>
                                </div>
                            </td>
                            <td class="cart__data" data-label="Kuantitas">
                                <form action="" method="post" class="cart__kuantitas-wrapper">
                                    <button type="submit" name="min" value="<?= $product["product_id"]; ?>" class="cart__kuantitas-minus">-</button>
                                    <p class="cart__kuantitas-number">
                                        <?= $product_quantity; ?>
                                    </p>
                                    <button type="submit" name="add" value="<?= $product["product_id"]; ?>" class="cart__kuantitas-plus">+</button>
                                </form>
                            </td>
                            <td class="cart__data" data-label="Total Harga">
                                <h3 class="cart__total-price">Rp. <?= number_format($total_price, 2, ",", "."); ?></h3>
                            </td>
                            <td class="cart__data" data-label="Aksi">
                                <a href="?del=<?= $product["product_id"]; ?>" class="cart__aksi">Hapus</a>
                            </td>
                        </tr>
                        <?php $no++; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <tr class="cart__row">
                    <?php if (!empty($products)) : ?>
                        <td class="cart__total-title" colspan="4">Sub Total</td>
                        <td class="cart__total-all-price" data-label="Sub Total" colspan="2">
                            Rp. <?= number_format($subtotal, 2, ",", "."); ?>
                        </td>
                    <?php else : ?>
                        <td class="cart__empty" colspan="6">Keranjang Kosong</td>
                    <?php endif; ?>
                </tr>
            </table>

            <div class="cart__nav">
                <div>
                    <a href="index.php" class="cart__nav-button btn">
                        < Kembali</a>
                </div>
                <?php if (!empty($products)) : ?>
                    <div>
                        <a href="order.php" class="cart__nav-button btn">Lanjutkan ></a>
                    </div>
                <?php endif; ?>
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