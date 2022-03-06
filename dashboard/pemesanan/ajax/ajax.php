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

$keyword = $_GET["keyword"];

$dataPerHalaman = 5;
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

// data hasil query untuk info
$dataAwal = $indexAwal + 1;
$dataAkhir = $indexAwal + count($data_order);
$jumlahSemuaData = count($jumlahDataQuery);

?>

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