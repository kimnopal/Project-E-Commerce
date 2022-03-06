<?php
session_start();

// cek session login
if (!isset($_SESSION["login"])) {
    header("Location: ../../login/login.php");
}


// query db
require '../../functions.php';

$data_jumbotron = query("SELECT * FROM jumbotron");
$data_aside = query("SELECT * FROM aside");
$data_about = query("SELECT * FROM about");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | TokoCoba</title>
    <!-- ICON -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <!-- CUSTOM CSS -->
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
                    <a href="../pemesanan/pemesanan.php" class="nav-field">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Pemesanan</p>
                    </a>
                    <a href="../customer/customer.php" class="nav-field">
                        <i class="fas fa-users"></i>
                        <p>Customer</p>
                    </a>
                    <a href="tampilan.php" class="nav-field active">
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
                    <h2>Tampilan</h2>
                    <!-- <div class="heading-field">
                        <form action="" method="post" class="search-field">
                            <input type="text" name="keyword" placeholder="Cari Kategori" autocomplete="off" value="<?= isset($_SESSION["keyword"]) ? $_SESSION["keyword"] : ""; ?>" id="keyword">
                            <button type="submit" name="search"><i class="fas fa-search"></i></button>
                        </form>
                        <a href="tambah-kategori/tambah-kategori.php" class="add-category">+ Tambah Kategori</a>
                    </div> -->
                </div>

                <div id="main-dashboard">
                    <div class="table-header">
                        <h3>Carousel</h3>
                        <a href="tambah-tampilan/tambah-tampilan.php" class="add-banner">+ Tambah Carousel</a>
                    </div>

                    <!-- ===== JUMBOTRON CAROUSEL ===== -->
                    <div class="table-container">
                        <table cellspacing="0" cellpadding="5">
                            <tr>
                                <th style="width: 10%;">No.</th>
                                <th>Gambar</th>
                                <th style="width: 20%;">Aksi</th>
                            </tr>

                            <?php $no = 1; ?>
                            <?php foreach ($data_jumbotron as $jumbotron) : ?>
                                <tr>
                                    <td><?= $no; ?></td>
                                    <td><img src="img/<?= $jumbotron["jumbotron_image"]; ?>" alt="" width="150px"></td>
                                    <td class="aksi">
                                        <a href="edit-tampilan/edit-tampilan.php?id=<?= $jumbotron["jumbotron_id"]; ?>&table=jumbotron">Edit</a>
                                        <a href="hapus-tampilan.php?id=<?= $jumbotron["jumbotron_id"]; ?>" onclick="return confirm('Apakah yakin menghapus kategori ini ?');">Hapus</a>
                                    </td>
                                    <?php $no++; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>

                    <!-- ===== ASIDE ===== -->
                    <h3>Aside</h3>
                    <div class="table-container">
                        <table cellspacing="0" cellpadding="5">
                            <tr>
                                <th style="width: 10%;">Keterangan</th>
                                <th>Gambar</th>
                                <th style="width: 20%;">Aksi</th>
                            </tr>

                            <tr>
                                <td>Right Top</td>
                                <td><img src="img/<?= $data_aside[0]["aside_image"]; ?>" alt="" width="150px"></td>
                                <td class="aksi">
                                    <a href="edit-tampilan/edit-tampilan.php?id=<?= $data_aside[0]["aside_id"]; ?>&table=aside">Edit</a>
                                    <a href=" hapus-tampilan-ab.php?id=<?= $data_aside[0]["aside_id"]; ?>&table=aside" onclick="return confirm('Apakah yakin menghapus kategori ini ?');">Hapus</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Right Bottom</td>
                                <td><img src="img/<?= $data_aside[1]["aside_image"]; ?>" alt="" width="150px"></td>
                                <td class="aksi">
                                    <a href="edit-tampilan/edit-tampilan.php?id=<?= $data_aside[1]["aside_id"]; ?>&table=aside">Edit</a>
                                    <a href="hapus-tampilan-ab.php?id=<?= $data_aside[1]["aside_id"]; ?>&table=aside" onclick="return confirm('Apakah yakin menghapus kategori ini ?');">Hapus</a>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- ===== ABOUT ===== -->
                    <h3>About</h3>
                    <div class="table-container">
                        <table cellspacing="0" cellpadding="5">
                            <tr>
                                <th style="width: 10%;">Keterangan</th>
                                <th>Gambar</th>
                                <th style="width: 20%;">Aksi</th>
                            </tr>

                            <tr>
                                <td>Left</td>
                                <td><img src="img/<?= $data_about[0]["about_image"]; ?>" alt="" width="150px"></td>
                                <td class="aksi">
                                    <a href="edit-tampilan/edit-tampilan.php?id=<?= $data_about[0]["about_id"]; ?>&table=about">Edit</a>
                                    <a href="hapus-tampilan-ab.php?id=<?= $data_about[0]["about_id"]; ?>&table=about" onclick="return confirm('Apakah yakin menghapus kategori ini ?');">Hapus</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Right Top</td>
                                <td><img src="img/<?= $data_about[1]["about_image"]; ?>" alt="" width="150px"></td>
                                <td class="aksi">
                                    <a href="edit-tampilan/edit-tampilan.php?id=<?= $data_about[1]["about_id"]; ?>&table=about">Edit</a>
                                    <a href="hapus-tampilan-ab.php?id=<?= $data_about[1]["about_id"]; ?>&table=about" onclick="return confirm('Apakah yakin menghapus kategori ini ?');">Hapus</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Right Bottom</td>
                                <td><img src="img/<?= $data_about[2]["about_image"]; ?>" alt="" width="150px"></td>
                                <td class="aksi">
                                    <a href="edit-tampilan/edit-tampilan.php?id=<?= $data_about[2]["about_id"]; ?>&table=about">Edit</a>
                                    <a href="hapus-tampilan-ab.php?id=<?= $data_about[2]["about_id"]; ?>&table=about" onclick="return confirm('Apakah yakin menghapus kategori ini ?');">Hapus</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

        </section>
    </div>

    <script src="js/script.js"></script>
</body>

</html>