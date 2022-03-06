<?php
if (isset($_GET["key"])) {
    // delete barang di cart.php
    if ($_GET["key"] == "quantity") {
        header("Location: cart.php");
        exit;
    }

    // pencet kategori di card produk
    if ($_GET["key"] == "category_id") {
        header("Location: search.php");
        exit;
    }

    // add barang ke cart di detail.php
    if ($_GET["key"] == "detail") {
        $product_id = $_GET["id"];
        header("Location: detail.php?id=$product_id");
        exit;
    }

    // add barang ke cart di search.php
    if ($_GET["key"] == "search") {
        header("Location: search.php");
        exit;
    }
}

header("Location: index.php");
