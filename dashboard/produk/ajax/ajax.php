<?php
session_start();

// cek session login
if (!isset($_SESSION["login"])) {
    header("Location: ../../../login/login.php");
}

require '../../../functions.php';

// set session search
if ($_GET["keyword"]) {
    $_SESSION["search"] = true;
} else {
    $_SESSION["search"] = false;
}

// set session keyword
$_SESSION["keyword"] = $_GET["keyword"];


// reset halaman jika keyword search baru
if (isset($_GET["page"])) {
    $_GET["page"] = $_POST["keyword"] != $_SESSION["keyword"] ? 1 : $_GET["page"];
}


$dataPerHalaman = 5;
$halamanAktif = isset($_GET["page"]) ? $_GET["page"] : 1;
$indexAwal = ($dataPerHalaman * $halamanAktif) - $dataPerHalaman;

// query
$data_product = cariProduk($_GET["keyword"], $indexAwal, $dataPerHalaman);

$keyword = $_GET["keyword"];
$jumlahDataQuery = query("SELECT * FROM product WHERE
                            product_name LIKE '%$keyword%' OR
                            product_price LIKE '%$keyword%' OR
                            product_description LIKE '%$keyword%' OR
                            product_status LIKE '%$keyword%'
                        ");
$jumlahHalaman = ceil(count($jumlahDataQuery) / $dataPerHalaman);

// data hasil query untuk info
$dataAwal = $indexAwal + 1;
$dataAkhir = $indexAwal + count($data_product);
$jumlahSemuaData = count($jumlahDataQuery);

?>

<div class="table-container">
    <table border="1" cellspacing="0" cellpadding="5">
        <tr>
            <th>No.</th>
            <th>Kategori</th>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th style="min-width: 20%; width: 30%;">Deskripsi</th>
            <th>Foto</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php $no = 1; ?>
        <?php foreach ($data_product as $product) : ?>
            <tr>
                <td><?= $no + $indexAwal; ?></td>
                <td><?= $product["category_name"]; ?></td>
                <td><?= $product["product_name"]; ?></td>
                <td><?= $product["product_price"]; ?></td>
                <td><?= $product["product_description"]; ?></td>
                <td><img src="img/<?= $product["product_image"]; ?>" style="width: 100px;"></td>
                <td><?= $product["product_status"] ? "Aktif" : "Tidak Aktif"; ?></td>
                <td class="aksi">
                    <a href="edit-produk/edit-produk.php?id=<?= $product["product_id"]; ?>">Edit</a>
                    <a href="hapus-produk.php?id=<?= $product["product_id"]; ?>" onclick="return confirm('Apakah yakin menghapus kategori ini ?');">Hapus</a>
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