<?php
session_start();

// cek session
if (!isset($_SESSION["login"])) {
    header("Location: ../../login/login.php");
}

// query db
require '../../../functions.php';

if (isset($_POST["submit"])) {
    $category_name = htmlspecialchars(ucwords(strtolower($_POST["category_name"])));
    $category_icon = htmlspecialchars($_POST["category_icon"]);
    $category_status = $_POST["category_status"];

    mysqli_query($conn, "INSERT INTO category VALUES 
            (null, '$category_name', '$category_icon', '$category_status')
        ");

    if (mysqli_affected_rows($conn)) {
        echo "<script>
                alert('Kategori berhasil ditambahkan');
                document.location.href = '../kategori.php';
            </script>";
    } else {
        echo "<script>
                alert('Kategori gagal ditambahkan');
                document.location.href = '../kategori.php';
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
                    <a href="../kategori.php" class="nav-field active">
                        <i class="fas fa-list-ul"></i>
                        <p>Kategori</p>
                    </a>
                    <a href="../../produk/produk.php" class="nav-field">
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
                    <h2>Tambah Kategori</h2>
                </div>
                <div class="table-container">
                    <form action="" method="post">
                        <div class="input-field">
                            <label for="category_name">Nama Kategori</label>
                            <input type="text" name="category_name" id="category_name" placeholder="Nama Kategori" required>
                        </div>
                        <div class="input-field">
                            <label for="category_icon">Icon Kategori</label>
                            <input type="text" name="category_icon" id="category_icon" placeholder="Icon Kategori" required>
                        </div>
                        <div class="input-field">
                            <label for="category_status">Status</label>
                            <select name="category_status" id="category_status">
                                <option value="Aktif" selected>Aktif</option>
                                <option value="Non-Aktif">Tidak Aktif</option>
                            </select>
                        </div>
                        <button type="submit" name="submit">Tambah Kategori</button>
                    </form>
                </div>
                <div class="dashboard-footer">
                    <a href="../kategori.php">Kembali</a>
                </div>
            </div>

        </section>
    </div>

    <script src="js/script.js"></script>
</body>

</html>