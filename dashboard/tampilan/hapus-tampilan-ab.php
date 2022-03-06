<?php
session_start();

// cek session login
if (!isset($_SESSION["login"])) {
    header("Location: ../login/login.php");
}

require '../../db.php';
require '../../functions.php';

$table = $_GET["table"];
$id = $_GET["id"];
$current_image = query("SELECT $table" . "_image FROM $table WHERE $table" . "_id = $id")[0][$table . "_image"];

if (hapusAsideAbout($id, $table) > 0) {
    unlink("img/$current_image");
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
