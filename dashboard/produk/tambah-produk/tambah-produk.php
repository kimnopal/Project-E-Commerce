<?php
session_start();

// cek session
if (!isset($_SESSION["login"])) {
    header("Location: ../../login/login.php");
}

// query db
require '../../../functions.php';

// query kategori
$data_category = query("SELECT * FROM category");

if (isset($_POST["submit"])) {
    if (tambah($_POST) > 0) {
        echo "<script>
                alert('Produk berhasil ditambahkan');
                document.location.href = '../produk.php';
            </script>";
    } else {
        echo "<script>
                alert('Produk gagal ditambahkan');
                document.location.href = '../produk.php';
            </script>";
    }
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
                    <a href="../produk.php" class="nav-field active">
                        <i class="fas fa-boxes"></i>
                        <p>Barang</p>
                    </a>
                    <a href="../../pemesanan/pemesanan.php" class="nav-field">
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
            <a href="../../../login/logout.php" class="nav-logout">
                <i class="fas fa-sign-out-alt"></i>
                <p>Log Out</p>
            </a>
        </nav>

        <section class="dashboard">
            <div class="dashboard-container">
                <h2>Dashboard</h2>
                <div class="dashboard-heading">
                    <h2>Tambah Produk</h2>
                </div>
                <div class="table-container">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="input-field">
                            <label for="category_id">Kategori</label>
                            <select name="category_id" id="category_id">
                                <option value="none" selected>-- Pilih Kategori --</option>
                                <?php foreach ($data_category as $category) : ?>
                                    <option value="<?= $category["category_id"]; ?>"><?= $category["category_name"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-field">
                            <label for="product_name">Nama Produk</label>
                            <input type="text" name="product_name" id="product_name" placeholder="Nama Produk" required>
                        </div>
                        <div class="input-field">
                            <label for="product_price">Harga Produk</label>
                            <input type="text" name="product_price" id="product_price" placeholder="Harga Produk" required>
                        </div>
                        <div class="input-field">
                            <label for="product_discount">Diskon Produk</label>
                            <input type="text" name="product_discount" id="product_discount" placeholder="Diskon Produk">
                        </div>
                        <div class="input-field">
                            <label for="product_description">Deskripsi Produk</label>
                            <textarea name="product_description" id="product_description" cols="30" rows="10"></textarea>
                        </div>
                        <div class="input-field">
                            <label for="product_image">Foto Produk</label>
                            <input type="file" name="product_image" id="product_image">
                        </div>
                        <div class="input-field">
                            <label for="product_status">Status</label>
                            <select name="product_status" id="product_status">
                                <option value="1" selected>Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                        <button type="submit" name="submit">Tambah Produk</button>
                    </form>
                </div>
                <div class="dashboard-footer">
                    <a href="../produk.php">Kembali</a>
                </div>
            </div>

        </section>
    </div>

    <script src="js/script.js"></script>
</body>

</html>