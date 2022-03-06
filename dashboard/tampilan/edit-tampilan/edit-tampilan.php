<?php
session_start();

// cek session
if (!isset($_SESSION["login"])) {
    header("Location: ../../login/login.php");
}

// cek id
// if (!isset($_GET["id"]) || $_GET["id"] == "") {
//     header("Location: ../kategori.php");
// }

// query db
require '../../../functions.php';

$id = $_GET["id"];
$table = $_GET["table"];
$data = query("SELECT * FROM $table WHERE $table" . "_id = $id")[0];

if (isset($_POST["upload"])) {

    if (ubahBanner($_POST, $id, $table) > 0) {
        echo "<script>
                alert('Banner berhasil diperbarui');
                document.location.href = '../tampilan.php';
            </script>";
    } else {
        echo "<script>
                alert('Banner gagal diperbarui');
                document.location.href = '../tampilan.php';
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
                    <a href="../tampilan.php" class="nav-field active">
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
                    <h2>Ubah Tampilan</h2>
                </div>
                <div class="table-container">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="input-field">
                            <label for="<?= $table; ?>_image">Upload Foto</label>
                            <img src="../img/<?= $data[$table . "_image"]; ?>" style="width: 100px; margin-bottom: 5px;">
                            <input type="hidden" name="<?= $table; ?>_image" value="<?= $data[$table . "_image"]; ?>">
                            <input type="file" name="<?= $table; ?>_image" id="<?= $table; ?>_image">
                        </div>
                        <?php if ($table === "jumbotron" || $table === "aside") : ?>
                            <div class="input-field">
                                <label for="<?= $table; ?>_link">Link</label>
                                <input type="text" name="<?= $table; ?>_link" value="<?= $data[$table . "_link"]; ?>">
                            </div>
                        <?php endif; ?>
                        <button type="submit" name="upload">Upload</button>
                    </form>
                </div>
                <div class="dashboard-footer">
                    <a href="../tampilan.php">Kembali</a>
                </div>
            </div>

        </section>
    </div>

    <script src="js/script.js"></script>
</body>

</html>