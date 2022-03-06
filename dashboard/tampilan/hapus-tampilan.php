<?php
session_start();

// cek session login
if (!isset($_SESSION["login"])) {
    header("Location: ../login/login.php");
}

require '../../db.php';
require '../../functions.php';

$id = $_GET["id"];

$jumbotron_image = query("SELECT jumbotron_image FROM jumbotron WHERE jumbotron_id = $id")[0]["jumbotron_image"];


if (hapus($id, "jumbotron", "jumbotron_id") > 0) {
    unlink("img/$jumbotron_image");
    echo "<script>
            alert('Banner berhasil dihapus');
            document.location.href = 'tampilan.php';
        </script>";
} else {
    echo "<script>
            alert('Banner gagal dihapus');
            document.location.href = 'tampilan.php';
        </script>";
}
