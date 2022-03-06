<?php
session_start();
require 'functions.php';

$login = isset($_SESSION["login"]);
unset($_SESSION["order"]);

// KATEGORI
$categories = query("SELECT * FROM category");

/* ========== PRODUCT BUTTON ========== */
// MENANGKAP category_id dari halaman lain / halaman sendiri (jika kategori diklik pada product card)
if (isset($_GET["id"])) {
    $_SESSION["filter"] = true;
    $_SESSION["category_id"] = $_GET["id"];
    $_SESSION["product_price_1"] = "";
    $_SESSION["product_price_2"] = "";
    $_SESSION["product_status"] = "";
    header("Location: everywhere.php?key=category_id");
}

// MENAMBAHKAN SESSION CART
// cek apakah tombol add ditekan
// $_GET["buy"] berisi id produk
if (isset($_GET["buy"])) {
    // cek apakah session produk sudah dipesan sebelumnya
    if (isset($_SESSION["cart"][$_GET["buy"]])) {
        // tambahkan jumlahnya
        $_SESSION["cart"][$_GET["buy"]]++;
        header("Location: everywhere.php?key=search");
        exit;
    } else {
        // masukkan produk ke session cart
        $_SESSION["cart"][$_GET["buy"]] = 1;
        header("Location: everywhere.php?key=search");
        exit;
    }
}

/* ========== CART SECTION ========== */
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

/* ========== SEARCH & FILTER SECTION ========== */
// CEK TOMBOL SEARCH / FILTER DITEKAN ATAU TIDAK
// search
if (isset($_POST["search"])) {
    $resetPage = true;
}

// filter
if (isset($_POST["filter"])) {
    $resetPage = true;
}

// CEK SESSION
// cek session search
if (isset($_SESSION["search"])) {
    if ($_SESSION["search"]) {
        $_POST["keyword_search"] = isset($_POST["keyword_search"]) ? htmlspecialchars($_POST["keyword_search"]) : $_SESSION["keyword_search"];
    }
}

// cek session filter
if (isset($_SESSION["filter"])) {
    if ($_SESSION["filter"]) {
        $_POST["filter"] = "";
        $_POST["category_id"] = isset($_POST["category_id"]) ? htmlspecialchars($_POST["category_id"]) : $_SESSION["category_id"];
        $_POST["product_price_1"] = isset($_POST["product_price_1"]) ? htmlspecialchars($_POST["product_price_1"]) : $_SESSION["product_price_1"];
        $_POST["product_price_2"] = isset($_POST["product_price_2"]) ? htmlspecialchars($_POST["product_price_2"]) : $_SESSION["product_price_2"];
        $_POST["product_status"] = isset($_POST["product_status"]) ? htmlspecialchars($_POST["product_status"]) : $_SESSION["product_status"];
    }
}

// MENANGKAP KEYWORD SEARCH
$keyword = isset($_POST["keyword_search"]) ? htmlspecialchars(($_POST["keyword_search"])) : "";

// CEK APAKAH ADA KEYWORD
if (empty($keyword)) {
    $_SESSION["search"] = false;
    $_SESSION["keyword_search"] = $keyword;
} else {
    $_SESSION["search"] = true;
    $_SESSION["keyword_search"] = $keyword;
}

// QUERY SEARCH
$query = "SELECT * FROM product 
            LEFT JOIN category 
            USING (category_id) 
            WHERE product_name 
            LIKE '%$keyword%'";

// FILTER
if (isset($_POST["filter"])) {
    // data filter
    $filter_category_id = htmlspecialchars($_POST["category_id"]);
    $filter_product_price_1 = htmlspecialchars($_POST["product_price_1"]);
    $filter_product_price_2 = htmlspecialchars($_POST["product_price_2"]);
    $filter_product_status = !empty(htmlspecialchars($_POST["product_status"])) ? "!=" : "";

    // pemanggilan fungsi filter
    $queryCategory = categoryFilter($filter_category_id);
    $queryPrice = priceFilter($filter_product_price_1, $filter_product_price_2);
    $queryDiscount = discountFilter($filter_product_status);

    // query data yang sudah difilter
    $query = "SELECT * FROM product
                LEFT JOIN category
                USING (category_id)
                WHERE product_name 
                LIKE '%$keyword%'
                AND $queryCategory
                AND $queryPrice
                AND $queryDiscount";

    // set session filter
    if (str_contains($queryCategory, "IS NOT NULL") && str_contains($queryPrice, "IS NOT NULL") && str_contains($queryDiscount, "IS NOT NULL")) {
        // ketika semua filter kosong
        $_SESSION["filter"] = false;
    } else {
        $_SESSION["filter"] = true;
    }

    $_SESSION["category_id"] = $filter_category_id;
    $_SESSION["product_price_1"] = $filter_product_price_1;
    $_SESSION["product_price_2"] = $filter_product_price_2;
    $_SESSION["product_status"] = $filter_product_status;
}

/* ========== PAGINATION SECTION ========== */
// ketika sudah ada page
if (isset($_GET["page"])) {
    // reset halaman aktif ketika tombol search / filter ditekan
    if (isset($resetPage)) {
        $_GET["page"] = 1;
        $_GET["nav"] = 1;
    }
}

// PRODUK
$productsNoLimit = query("$query
                        AND product_status != 0
                        ORDER BY product_price");

// PAGINATION PRODUK
$jumlahDataPerHalaman = 12;
// jumlah halaman didapat dari jumlah seluruh data dibagi data per halaman 
$jumlahSeluruhHalaman = ceil(count($productsNoLimit) / $jumlahDataPerHalaman);
$halamanAktif = (isset($_GET["page"])) ? $_GET["page"] : 1;
// index awal mulai dari 0, 8, 16, 24, 32, ... // bisa didapatkan dengan rumus a + (n - 1) b atau (n . b) - b + a
$indexAwal = ($halamanAktif * $jumlahDataPerHalaman) - $jumlahDataPerHalaman;

// PAGINATION NAV
$jumlahNavPerHalaman = 2;
// jumlah halaman nav didapat dari jumlah seluruh halaman dibagi dengan banyak nav per halaman
$jumlahSeluruhHalamanNav = ceil($jumlahDataPerHalaman / $jumlahNavPerHalaman);
$halamanNavAktif = (isset($_GET["nav"])) ? $_GET["nav"] : 1;
// index awal mulai dari 1, 3, 5, 7, 9, ... // bisa didapatkan dengan rumus a + (n - 1) b atau (n . b) - b + a
$navAwal = ($halamanNavAktif * $jumlahNavPerHalaman) - $jumlahNavPerHalaman + 1;

// PRODUK LIMIT
$productsLimit = query("$query 
                    AND product_status != 0
                    ORDER BY product_price
                    LIMIT $indexAwal, $jumlahDataPerHalaman");

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
    <link rel="stylesheet" href="css/filter.css">
    <link rel="stylesheet" href="css/result.css">
    <link rel="stylesheet" href="css/product.css">
    <link rel="stylesheet" href="css/pagination.css">
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
                <form action="" method="post" class="nav__search">
                    <input type="text" name="keyword_search" id="search" class="nav__search-field" value="<?= ($keyword) ? $keyword : ""; ?>" placeholder="Search">
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

    <!-- ========== FILTER ========== -->
    <section class="filter container">
        <div class="filter__container">
            <h3 class="filter__title">Filter</h3>
            <form action="" method="post" class="filter__main-container">
                <div class="filter__main">
                    <div class="filter__input-field">
                        <label for="category_id" class="filter__input-title">Kategori</label>
                        <select name="category_id" id="category_id" class="filter__input">
                            <!-- cek apakah sebelumnya menggunakan filter -->
                            <?php if (isset($filter_category_id) && !empty($filter_category_id)) : ?>
                                <option value="">Semua</option>
                            <?php else : ?>
                                <option value="" selected>Semua</option>
                            <?php endif; ?>
                            <!-- ======================================= -->

                            <!-- pengulangan untuk opsi kategori -->
                            <?php foreach ($categories as $category) : ?>
                                <!-- jika sebelumnya menggunakan filter maka kategori yang dipilih auto select -->
                                <?php if (isset($filter_category_id) && !empty($filter_category_id) && $category["category_id"] == $filter_category_id) : ?>
                                    <option value="<?= $category["category_id"]; ?>" selected><?= $category["category_name"]; ?></option>
                                <?php continue;
                                endif; ?>
                                <!-- ========================================================================= -->

                                <option value="<?= $category["category_id"]; ?>"><?= $category["category_name"]; ?></option>
                            <?php endforeach; ?>
                            <!-- ================================ -->
                        </select>
                    </div>

                    <div class="filter__input-field">
                        <label for="product_price_1" class="filter__input-title">Harga</label>
                        <div class="filter__input-container">
                            <input type="text" name="product_price_1" id="product_price_1" class="filter__input" value="<?= (isset($filter_product_price_1)) ? $filter_product_price_1 : ""; ?>">
                            <p class="filter__input-text">sampai</p>
                            <input type="text" name="product_price_2" id="product_price_2" class="filter__input" value="<?= (isset($filter_product_price_2)) ? $filter_product_price_2 : ""; ?>">
                        </div>
                    </div>

                    <div class=" filter__input-field">
                        <label for="product_status" class="filter__input-title">Status</label>
                        <select name="product_status" id="product_status" class="filter__input">
                            <!-- cek apakah filter status dipilih -->
                            <?php if (isset($filter_product_status)) : ?>
                                <!-- cek apakah filter status dipilih diskon -->
                                <?php if (!empty($filter_product_status)) : ?>
                                    <option value="">Semua</option>
                                    <option value="discount" selected>Diskon</option>
                                <?php else : ?>
                                    <option value="" selected>Semua</option>
                                    <option value="discount">Diskon</option>
                                <?php endif; ?>
                                <!-- ====================================== -->
                            <?php else : ?>
                                <option value="" selected>Semua</option>
                                <option value="discount">Diskon</option>
                            <?php endif; ?>
                            <!-- ================================ -->
                        </select>
                    </div>
                </div>

                <button type="submit" name="filter" class="filter__input-search">Filter</button>
            </form>
        </div>
    </section>

    <!-- ========== Result ========== -->
    <?php if ($keyword) : ?>
        <section class="result container">
            <h2 class="result__text">Hasil Pencarian "<strong><?= $keyword; ?></strong>"</h2>
        </section>
    <?php endif; ?>

    <!-- ========== Produk ========== -->
    <section class="product container">
        <div class="product__container">
            <?php if (count($productsLimit) == 0) : ?>
                <div class="data__not-found-container">
                    <img src="img/searching.png" alt="" class="data__not-found-img">
                    <p class="data__not-found-text">Produk tidak ditemukan</p>
                </div>
            <?php endif; ?>

            <?php foreach ($productsLimit as $product) : ?>
                <div class="product__card card">
                    <div class="product__card-image card__image-container"><img src="dashboard/produk/img/<?= $product["product_image"]; ?>" class="card__image" alt=""></div>
                    <div class="card__text">
                        <a href="?buy=<?= $product["product_id"]; ?>" class="card__add-cart">+</a>
                        <a href="?id=<?= $product["category_id"]; ?>" class="card__category"><?= $product["category_name"]; ?></a>
                        <a href="detail.php?id=<?= $product["product_id"]; ?>" class="card__title"><?= $product["product_name"]; ?></a>
                        <a href="detail.php?id=<?= $product["product_id"]; ?>" class="card__price-container">
                            <div class="card__disc" style="display: <?= $product["product_discount"] ? "initial" : "none"; ?>;">
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
    </section>

    <section class="pagination container">
        <div class="pagination__container">
            <?php if (count($productsLimit) > 0) : ?>
                <p class="pagination__info-page">Showing <?= $indexAwal + 1; ?> - <?= $indexAwal + count($productsLimit); ?> of <?= count($productsNoLimit); ?> entries</p>
            <?php endif; ?>

            <div class="pagination__nav-page">

                <?php if ($halamanAktif > 1) : ?>
                    <a href="?page=<?= $halamanAktif - 1; ?>&nav=<?= ($halamanAktif % 2 == 0 || $halamanAktif == $navAwal) && $halamanNavAktif > 1 ? $halamanNavAktif - 1 : $halamanNavAktif; ?>" class="pagination__nav-prev pagination__btn">previous</a>
                <?php endif; ?>

                <?php for ($i = $navAwal; $i <= $jumlahSeluruhHalaman; $i++) : ?>
                    <?php if ($i % 2 != 0 && $i != $navAwal && $i < $jumlahSeluruhHalaman) : ?>
                        <?php if ($i == $halamanAktif) : ?>
                            <a href="?page=<?= $i; ?>&nav=<?= $halamanNavAktif + 1; ?>" class="pagination__nav-number pagination__btn pagination__active">
                                <?= $i; ?>
                            </a>
                        <?php else : ?>
                            <a href="?page=<?= $i; ?>&nav=<?= $halamanNavAktif + 1; ?>" class="pagination__nav-number pagination__btn">
                                <?= $i; ?>
                            </a>
                        <?php endif; ?>
                        <?php break; ?>
                    <?php elseif ($i != 1 && $i == $navAwal) : ?>
                        <?php if ($i == $halamanAktif) : ?>
                            <a href="?page=<?= $i; ?>&nav=<?= $halamanNavAktif - 1; ?>" class="pagination__nav-number pagination__btn pagination__active">
                                <?= $i; ?>
                            </a>
                        <?php else : ?>
                            <a href="?page=<?= $i; ?>&nav=<?= $halamanNavAktif - 1; ?>" class="pagination__nav-number pagination__btn">
                                <?= $i; ?>
                            </a>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php if ($i == $halamanAktif) : ?>
                            <a href="?page=<?= $i; ?>&nav=<?= $halamanNavAktif; ?>" class="pagination__nav-number pagination__btn pagination__active">
                                <?= $i; ?>
                            </a>
                        <?php else : ?>
                            <a href="?page=<?= $i; ?>&nav=<?= $halamanNavAktif; ?>" class="pagination__nav-number pagination__btn">
                                <?= $i; ?>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($halamanAktif < $jumlahSeluruhHalaman) : ?>
                    <a href="?page=<?= $halamanAktif + 1; ?>&nav=<?= ($halamanAktif % 2 == 0 || $halamanAktif != $navAwal) && $halamanNavAktif < $jumlahSeluruhHalamanNav ? $halamanNavAktif + 1 : $halamanNavAktif; ?>" class="pagination__nav-next pagination__btn">next</a>
                <?php endif; ?>
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