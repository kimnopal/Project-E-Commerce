<?php
session_start();

// cek session login
if (!isset($_SESSION["login"])) {
    header("Location: ../login/login.php");
}


// cek session search
if (isset($_SESSION["search"])) {
    // cek apakah session search bernilai true, jika iya maka mode search aktif
    if ($_SESSION["search"]) {
        $_POST["search"] = "";
        $_POST["keyword"] = isset($_POST["keyword"]) ? $_POST["keyword"] : $_SESSION["keyword"];
    }
}


// query db
require '../../functions.php';

$jumlahDataQuery = query("SELECT * FROM payment LEFT JOIN order_db USING (order_sign) WHERE order_status = 'Proses Verifikasi'");

// pagination
$dataPerHalaman = 5;
$jumlahHalaman = ceil(count($jumlahDataQuery) / $dataPerHalaman);
$halamanAktif = isset($_GET["page"]) ? $_GET["page"] : 1;
$indexAwal = ($dataPerHalaman * $halamanAktif) - $dataPerHalaman;

$query = "SELECT * FROM payment LEFT JOIN order_db USING (order_sign) WHERE order_status = 'Proses Verifikasi' ORDER BY payment_date LIMIT $indexAwal, $dataPerHalaman";

$data_order = query($query);

// search
if (isset($_POST["search"])) {
    // set session search
    if ($_POST["keyword"]) {
        $_SESSION["search"] = true;
    } else {
        $_SESSION["search"] = false;
    }

    // set session keyword
    $_SESSION["keyword"] = $_POST["keyword"];

    $keyword = $_POST["keyword"];


    // reset halaman jika keyword search baru
    if (isset($_GET["page"])) {
        $_GET["page"] = $_POST["keyword"] != $_SESSION["keyword"] ? 1 : $_GET["page"];
    }

    $halamanAktif = isset($_GET["page"]) ? $_GET["page"] : 1;
    $indexAwal = ($dataPerHalaman * $halamanAktif) - $dataPerHalaman;

    $data_order = query("SELECT * FROM payment LEFT JOIN order_db USING (order_sign) WHERE 
                            order_status = 'Proses Verifikasi' AND
                            order_sign LIKE '%$keyword%' OR
                            order_status = 'Proses Verifikasi' AND
                            payment_date LIKE '%$keyword%'
                            ORDER BY payment_date
                            LIMIT $indexAwal, $dataPerHalaman
                            ");

    $jumlahDataQuery = query("SELECT * FROM payment LEFT JOIN order_db USING (order_sign) WHERE
                                order_status = 'Proses Verifikasi' AND
                                order_sign LIKE '%$keyword%' OR
                                order_status = 'Proses Verifikasi' AND
                                payment_date LIKE '%$keyword%'
                            ");
    $jumlahHalaman = ceil(count($jumlahDataQuery) / $dataPerHalaman);
}


// data hasil query untuk info
$dataAwal = $indexAwal + 1;
$dataAkhir = $indexAwal + count($data_order);
$jumlahSemuaData = count($jumlahDataQuery);
if ($jumlahSemuaData == 0) {
    $dataAwal = 0;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | TokoCoba</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <div class="container">
        <nav>
            <div class="nav-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="nav-main">
                <div class="nav-brand">
                    <h2 class="brand-name">Toko<span>Coba</span></h2>
                    <h2 class="brand-logo">T<span>C</span></h2>
                </div>
                <div class="nav-field-container">
                    <a href="../kategori/kategori.php" class="nav-field">
                        <i class="fas fa-list-ul"></i>
                        <p>Kategori</p>
                    </a>
                    <a href="../produk/produk.php" class="nav-field">
                        <i class="fas fa-boxes"></i>
                        <p>Barang</p>
                    </a>
                    <a href="pemesanan.php" class="nav-field active">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Pemesanan</p>
                    </a>
                    <a href="../customer/customer.php" class="nav-field">
                        <i class="fas fa-users"></i>
                        <p>Customer</p>
                    </a>
                    <a href="../tampilan/tampilan.php" class="nav-field">
                        <i class="fas fa-th-large"></i>
                        <p>Tampilan</p>
                    </a>
                </div>
            </div>
            <a href="../../login/logout.php" class="nav-logout">
                <i class="fas fa-sign-out-alt"></i>
                <p>Log Out</p>
            </a>
        </nav>

        <section class="dashboard">
            <div class="dashboard-container">
                <h2>Dashboard</h2>
                <div class="dashboard-heading">
                    <h2>Produk</h2>
                    <div class="heading-field">
                        <form action="" method="post" class="search-field">
                            <input type="text" name="keyword" placeholder="Cari Produk" autocomplete="off" value="<?= isset($_SESSION["keyword"]) ? $_SESSION["keyword"] : ""; ?>" id="keyword">
                            <button type="submit" name="search"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>

                <div id="main-dashboard">
                    <div class="table-container">
                        <table cellspacing="0">
                            <tr>
                                <th>No.</th>
                                <th>Order ID</th>
                                <th>Tanggal Pembayaran</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>

                            <?php $no = 1; ?>
                            <?php foreach ($data_order as $order) : ?>
                                <tr>
                                    <td><?= $no + $indexAwal; ?></td>
                                    <td><?= $order["order_sign"]; ?></td>
                                    <td><?= $order["payment_date"]; ?></td>
                                    <td>Rp. <?= number_format($order["order_totalprice"], 2, ",", "."); ?></td>
                                    <td><?= $order["order_status"]; ?></td>
                                    <td class="aksi">
                                        <a href="detail-pemesanan/detail-pemesanan.php?id=<?= ltrim($order["order_sign"], "#"); ?>">Detail</a>
                                    </td>
                                    <?php $no++; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>

                    <div class="dashboard-footer">
                        <p>Showing <?= $dataAwal; ?> to <?= $dataAkhir; ?> of <?= $jumlahSemuaData; ?> entries</p>
                        <div class="pagination">
                            <?php
                            // pagination navigation
                            $paginPerHalaman = 2;
                            $jumlahHalamanPagin = ceil($jumlahHalaman / $paginPerHalaman);
                            $paginAktif = isset($_GET["pagin"]) ? $_GET["pagin"] : 1;
                            $paginAwal = ($paginPerHalaman * $paginAktif) - $paginPerHalaman;
                            ?>
                            <?php if ($halamanAktif > 1) : ?>
                                <a href="?page=<?= $halamanAktif - 1; ?>&pagin=<?= $halamanAktif == $paginAwal + 1 && $paginAwal > 1 ? $paginAktif - 1 : $paginAktif; ?>">previous</a>
                            <?php endif; ?>


                            <!-- pengulangan untuk banyak halaman nav -->
                            <?php for ($i = $paginAktif; $i <= $jumlahHalamanPagin; $i++) : ?>
                                <!-- pengulangan untuk 3 buah nav di tiap halaman -->
                                <?php for ($j = $paginAwal + 1; $j <= $jumlahHalaman; $j++) : ?>
                                    <!-- pengecekan apakah ini nav terakhir, jika iya maka stop looping -->
                                    <?php if ($j % 2 != 0 && $j != $paginAwal + 1 && $j < $jumlahHalaman) : ?>
                                        <?php if ($j == $halamanAktif) : ?>
                                            <a href="?page=<?= $j; ?>&pagin=<?= $i + 1; ?>" class="number active">
                                                <?= $j; ?>
                                            </a>
                                        <?php else : ?>
                                            <a href="?page=<?= $j; ?>&pagin=<?= $i + 1; ?>" class="number">
                                                <?= $j; ?>
                                            </a>
                                        <?php endif; ?>
                                        <?php break; ?>
                                    <?php endif; ?>

                                    <!-- pengecekan apakah ini nav awal -->
                                    <?php if ($i != 1 && $j == $paginAwal + 1) : ?>
                                        <?php if ($j == $halamanAktif) : ?>
                                            <a href="?page=<?= $j; ?>&pagin=<?= $i - 1; ?>" class="number active">
                                                <?= $j; ?>
                                            </a>
                                            <?php continue; ?>
                                        <?php else : ?>
                                            <a href="?page=<?= $j; ?>&pagin=<?= $i - 1; ?>" class="number">
                                                <?= $j; ?>
                                            </a>
                                            <?php continue; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($j == $halamanAktif) : ?>
                                        <a href="?page=<?= $j; ?>&pagin=<?= $i; ?>" class="number active">
                                            <?= $j; ?>
                                        </a>
                                    <?php else : ?>
                                        <a href="?page=<?= $j; ?>&pagin=<?= $i; ?>" class="number">
                                            <?= $j; ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <?php break; ?>
                            <?php endfor; ?>

                            <?php if ($halamanAktif < $jumlahHalaman) : ?>
                                <a href="?page=<?= $halamanAktif + 1; ?>&pagin=<?= $halamanAktif != $paginAwal + 1 && $halamanAktif < $jumlahHalaman - 1 ? $paginAktif + 1 : $paginAktif; ?>">next</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>

    <script src="js/script.js"></script>
</body>

</html>