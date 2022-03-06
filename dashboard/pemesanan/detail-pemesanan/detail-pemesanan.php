<?php
session_start();

// cek session
if (!isset($_SESSION["login"])) {
    header("Location: ../../../login/login.php");
}

// cek id
// if (!isset($_GET["id"]) || $_GET["id"] == "") {
//     header("Location: ../produk.php");
// }

// query db
require '../../../functions.php';

// $product_id = $_GET["id"];

// $data_product = query("SELECT * FROM product WHERE product_id = $product_id")[0];
// $data_category = query("SELECT * FROM category");
if (!isset($_GET["id"])) {
    header("Location: ../pemesanan.php");
}

$sign = '#' . $_GET["id"];

$order_status = query("SELECT order_status FROM order_db WHERE order_sign = '$sign'")[0]["order_status"];

// CEK STATUS PEMESANAN
if ($order_status != 'Proses Verifikasi') {
    header("Location: ../pemesanan.php");
}

$order_data = query("SELECT * FROM order_db WHERE order_sign = '$sign'")[0];
$payment = query("SELECT * FROM payment WHERE order_sign = '$sign'")[0];
$products = query("SELECT * FROM order_product LEFT JOIN product USING (product_id) WHERE order_sign = '$sign'");


// DATA PENGIRIMAN
// dimasukkan kedalam tabel order_db
$order_sign = $order_data["order_sign"];
$order_userid = $order_data["order_userid"];
$username = query("SELECT * FROM user WHERE user_id = $order_userid")[0]["user_username"];
$order_username = $username;
$order_name = $order_data["order_name"];
$order_address = $order_data["order_address"];
$order_telephone = $order_data["order_telephone"];
$order_date = $order_data["order_date"];
$order_totalprice = $order_data["order_totalprice"];
$payment_date = $payment["payment_date"];
$payment_account = $payment["payment_account"];
$payment_account_name = $payment["payment_account_name"];

if (isset($_POST["update"])) {
    $order_status = $_POST["order_status"];

    mysqli_query($conn, "UPDATE order_db SET
                        order_status = '$order_status'
                        WHERE order_sign = '$order_sign'
                        ");
    header("Location: ../pemesanan.php");
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
                    <a href="../../kategori/kategori.php" class="nav-field">
                        <i class="fas fa-list-ul"></i>
                        <p>Kategori</p>
                    </a>
                    <a href="../../produk/produk.php" class="nav-field">
                        <i class="fas fa-boxes"></i>
                        <p>Barang</p>
                    </a>
                    <a href="../pemesanan.php" class="nav-field active">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Pemesanan</p>
                    </a>
                    <a href="../../customer/customer.php" class="nav-field">
                        <i class="fas fa-users"></i>
                        <p>Customer</p>
                    </a>
                    <a href="../../tampilan/tampilan.php" class="nav-field">
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
                    <h2>Detail Pemesanan</h2>
                </div>
                <div class="receipt__detail">
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
                        <tr>
                            <th class="receipt__subtitle">Tanggal Pembayaran</th>
                            <td class="receipt__equal">:</td>
                            <td class="receipt__content"><?= $payment_date; ?> WIB</td>
                        </tr>
                        <tr>
                            <th class="receipt__subtitle">Nomor Rekening</th>
                            <td class="receipt__equal">:</td>
                            <td class="receipt__content"><?= $payment_account; ?></td>
                        </tr>
                        <tr>
                            <th class="receipt__subtitle">Nama Rekening</th>
                            <td class="receipt__equal">:</td>
                            <td class="receipt__content"><?= $payment_account_name; ?></td>
                        </tr>
                    </table>
                </div>
                <form action="" method="post" class="form-input">
                    <div class="input-field">
                        <label for="order_status">Status</label>
                        <select name="order_status" id="order_status">
                            <option value="Proses Verifikasi" selected>Proses Verifikasi</option>
                            <option value="Lunas">Lunas</option>
                        </select>
                        <button type="submit" name="update">Update</button>
                    </div>
                </form>
                <div class="receipt__order">
                    <div class="table-container">
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
                    <div class="dashboard-footer">
                        <a href="../pemesanan.php">Kembali</a>
                    </div>
                </div>

            </div>


        </section>
    </div>

    <script src="js/script.js"></script>
</body>

</html>