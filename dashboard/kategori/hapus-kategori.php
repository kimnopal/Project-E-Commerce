<?php
session_start();

// cek session login
if (!isset($_SESSION["login"])) {
    header("Location: ../login/login.php");
}

require '../../db.php';
require '../../functions.php';

$id = $_GET["id"];

if (hapus($id, "category", "category_id") > 0) {
    echo "<script>
            alert('Kategori berhasil dihapus');
            document.location.href = 'kategori.php';
        </script>";
} else {
    echo "<script>
            alert('Kategori gagal dihapus');
            document.location.href = 'kategori.php';
        </script>";
}
