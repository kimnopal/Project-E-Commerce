<?php
session_start();

// cek session login
if (!isset($_SESSION["login"])) {
    header("Location: ../../login/login.php");
}

require '../../db.php';
require '../../functions.php';

$id = $_GET["id"];

$product_image = query("SELECT product_image FROM product WHERE product_id = $id")[0]["product_image"];

if (hapus($id, "product", "product_id") > 0) {
    unlink("img/$product_image");
    echo "<script>
            alert('Produk berhasil dihapus');
            document.location.href = 'produk.php';
        </script>";
} else {
    echo "<script>
            alert('Produk gagal dihapus');
            document.location.href = 'produk.php';
        </script>";
}
