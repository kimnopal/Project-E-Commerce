<?php

// connect ke db
require 'db.php';

// function untuk query
function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);

    $rows = [];

    // fetch data query menjadi array associative
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

// function untuk cek username
function cekUsername($username)
{
    // cek username di tabel admin
    $query = "SELECT * FROM admin WHERE username = '$username'";
    $result = count(query($query));
    if ($result === 1) {
        return "admin";
    }

    // cek username di tabel user
    $query = "SELECT * FROM user WHERE user_username = '$username'";
    if (count(query($query))) {
        return "user";
    }

    return "error";
}

// function untuk register
function register($data)
{
    global $conn;

    // tampung semua data ke variable
    $username = htmlspecialchars(strtolower(stripslashes($data["user_username"])));
    $email = mysqli_real_escape_string($conn, $data["user_email"]);
    $password = mysqli_real_escape_string($conn, $data["user_password"]);

    // cek apakah username sudah ada di tabel user
    $result = mysqli_query($conn, "SELECT * FROM user WHERE user_username = '$username'");
    if (mysqli_num_rows($result)) {
        return "username";
    }

    // cek apakah email sudah ada di tabel user
    $result = mysqli_query($conn, "SELECT * FROM user WHERE user_email = '$email'");
    if (mysqli_num_rows($result)) {
        return "email";
    }

    // hashing password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // masukkan password ke db
    mysqli_query($conn, "INSERT INTO user VALUES(null, '$username', '$email', '$password')");

    // return mysqli_affected_rows($conn);
    return "berhasil";
}

// function untuk mengubah kategori
function ubah($data, $id)
{
    global $conn;

    $category_name = htmlspecialchars(ucwords(strtolower($data["category_name"])));
    $category_icon = htmlspecialchars($data["category_icon"]);
    $category_status = htmlspecialchars($data["category_status"]);

    mysqli_query($conn, "UPDATE category SET 
                    category_name = '$category_name',
                    category_icon = '$category_icon',
                    category_status = '$category_status'
                WHERE category_id = $id
                ");

    return mysqli_affected_rows($conn);
}

// function untuk mengubah produk
function ubahProduk($data, $id)
{
    global $conn;

    $category_id = htmlspecialchars(ucwords(strtolower($data["category_id"])));
    $product_name = htmlspecialchars(ucwords(strtolower($data["product_name"])));
    $product_price = htmlspecialchars($data["product_price"]);
    $product_discount = htmlspecialchars($data["product_discount"]);
    $product_description = htmlspecialchars($data["product_description"]);
    $product_image_lama = htmlspecialchars($data["product_image"]);
    $product_status = htmlspecialchars($data["product_status"]);

    // cek apakah user upload gambar baru
    if ($_FILES["product_image"]["error"] == 4) {
        $product_image = $product_image_lama;
    } else {
        $product_image = upload("product");
    }

    $query = "UPDATE product SET
                category_id = $category_id,
                product_name = '$product_name',
                product_price = $product_price,
                product_discount = '$product_discount',
                product_description = '$product_description',
                product_image = '$product_image',
                product_status = $product_status
            WHERE product_id = $id
            ";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

// function untuk menghapus data dari tabel
function hapus($id, $table, $table_id)
{
    global $conn;

    $query = "DELETE FROM $table WHERE $table_id = $id";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

// function untuk searching kategori
function cari($keyword, $indexAwal, $dataPerHalaman)
{
    $query = "SELECT * FROM category WHERE
                category_name LIKE '%$keyword%' OR
                category_status LIKE '%$keyword%'
                LIMIT $indexAwal, $dataPerHalaman 
            ";

    return query($query);
}

// function untuk searching produk
function cariProduk($keyword, $indexAwal, $dataPerHalaman)
{
    $query = "SELECT * FROM product LEFT JOIN category USING (category_id) WHERE
                category_name LIKE '%$keyword%' OR
                product_name LIKE '%$keyword%' OR
                product_price LIKE '%$keyword%' OR
                product_description LIKE '%$keyword%' OR
                product_status LIKE '%$keyword%'
                LIMIT $indexAwal, $dataPerHalaman
            ";

    return query($query);
}

// function untuk menambahkan produk
function tambah($data)
{
    global $conn;

    $category_id = htmlspecialchars($data["category_id"]);
    $product_name = htmlspecialchars(ucwords(strtolower($data["product_name"])));
    $product_price = htmlspecialchars($data["product_price"]);
    $product_discount = htmlspecialchars($data["product_discount"]);
    $product_description = htmlspecialchars($data["product_description"]);
    $product_status = htmlspecialchars($data["product_status"]);

    $product_image = upload("product");
    if (!$product_image) {
        return false;
    }

    mysqli_query($conn, "INSERT INTO product VALUES 
            (null, '$category_id', '$product_name', '$product_price', '$product_discount', '$product_description', '$product_image', '$product_status', null)
        ");

    return mysqli_affected_rows($conn);
}

// function untuk validasi gambar
function upload($data)
{
    // tangkap isi variable $_FILES
    $namaFile = $_FILES[$data . "_image"]["name"];
    $ukuranFile = $_FILES[$data . "_image"]["size"];
    $error = $_FILES[$data . "_image"]["error"];
    $tmpName = $_FILES[$data . "_image"]["tmp_name"];

    // cek gambar yang di upload
    if ($error == 4) {
        echo "<script>
                alert('Pilih gambar terlebih dahulu');
            </script>";
        return false;
    }

    // cek ekstensi file yang di upload
    $ekstensiGambarValid = ["jpg", "jpeg", "png"];
    $ekstensiGambar = explode('.', $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));

    if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
        echo "<script>
                alert('Gambar yang dipilih tidak valid, pastikan berekstensi jpg, jpeg, atau png');
            </script>";
        return false;
    }

    // cek ukuran file
    if ($ukuranFile > 2000000) {
        echo "<script>
                alert('Ukuran gambar terlalu besar');
            </script>";
        return false;
    }

    // setelah lolos pengecekan
    $namaFileBaru = uniqid(time());
    $namaFileBaru .= ".$ekstensiGambar";
    move_uploaded_file($tmpName, "../img/$namaFileBaru");

    return $namaFileBaru;
}

// function untuk ubah gambar jumbotron, aside, dan about 
function ubahBanner($data, $id, $table)
{
    global $conn;

    $banner_image_lama = $data[$table . "_image"];

    // cek apakah user upload gambar baru
    if ($_FILES[$table . "_image"]["error"] == 4) {
        $banner_image = $banner_image_lama;
    } else {
        $banner_image = upload($table);
        if (!empty($banner_image_lama)) {
            unlink("../img/" . $banner_image_lama);
        }
    }

    $query = "UPDATE $table SET
                $table" . "_image = '$banner_image'
            WHERE $table" . "_id = $id
            ";

    if ($table == "jumbotron" || $table == "aside") {
        $banner_link = $data[$table . "_link"];
        $query = "UPDATE $table SET
                $table" . "_image = '$banner_image',
                $table" . "_link = '$banner_link'
                WHERE $table" . "_id = $id
                ";
    }

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

// function untuk menghapus aside dan about 
function hapusAsideAbout($id, $table)
{
    global $conn;

    mysqli_query($conn, "UPDATE $table 
                    SET $table" . "_image = ''
                WHERE $table" . "_id = $id
                ");
    return mysqli_affected_rows($conn);
}

// function untuk filtering kategori
function categoryFilter($category_id)
{
    // kondisi jika kategori dipilih
    if (!empty($category_id)) {
        return "category_id = $category_id";
    }

    // kondisi jika kategori tidak dipilih
    return "category_id IS NOT NULL";
}

// function untuk filtering diskon
function discountFilter($discount)
{
    // kondisi jika diskon dipilih
    if (!empty($discount)) {
        return "product_discount $discount ''";
    }

    // kondisi jika diskon tidak dipilih
    return "product_discount IS NOT NULL";
}

// function untuk filtering harga
function priceFilter($min_price, $max_price)
{
    // filter karakter selain angka
    $min_price = filter_var($min_price, FILTER_SANITIZE_NUMBER_INT);
    $max_price = filter_var($max_price, FILTER_SANITIZE_NUMBER_INT);

    // kondisi jika kedua range harga diisi
    if ((!empty($min_price) && !empty($max_price)) || ($min_price == '0' && $max_price != '') || ($max_price == '0' && $min_price != '')) {
        return "product_price BETWEEN $min_price AND $max_price";
    }

    // kondisi jika kedua range hanya min harga yang diisi
    if (!empty($min_price) || $min_price == '0') {
        return "product_price >= $min_price ORDER BY product_price";
    }

    // kondisi jika kedua range hanya max harga yang diisi
    if (!empty($max_price) || $max_price == '0') {
        return "product_price <= $max_price ORDER BY product_price";
    }

    // kondisi jika kedua range harga tidak diisi
    return "product_price IS NOT NULL";
}

// function untuk menghitung kuantitas produk jika ditambah atau dikurang
function quantity()
{
    global $conn;

    // tambah dan kurang kuantitas

    // cek jika tombol + ditekan
    if (isset($_POST["add"])) {
        $product_id = $_POST["add"];

        // cek apakah ada cart dan ada isinya (belum login)
        if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
            // menambahkan kuantitas produk didalam cart sesuai id produk
            $_SESSION["cart"][$product_id]++;
        } else {
            // jika sudah login

            // query update untuk mengupdate kuantitas dan total harga produk di db
            mysqli_query($conn, "UPDATE cart SET 
                                    cart_quantity = cart_quantity + 1,
                                    cart_totalprice = cart_quantity * cart_price
                                WHERE product_id = $product_id");
        }

        // reset post
        header("Location: everywhere.php?key=quantity");
    } elseif (isset($_POST["min"])) {
        // jika tombol - ditekan

        $product_id = $_POST["min"];

        // cek apakah ada cart dan ada isinya (belum login)
        if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
            // cek apakah jumlahnya sama dengan 1, jika iya maka kuantitas tidak bisa dikurangi lagi
            if ($_SESSION["cart"][$product_id] == 1) {
                echo "<script>
                            alert('Jumlah Barang Tidak Boleh 0 !');
                            document.location.href = 'everywhere.php?key=quantity';
                        </script>";
                exit;
            }

            // mengurangi kuantitas produk didalam cart sesuai id produk
            $_SESSION["cart"][$product_id]--;
        } else {
            // jika sudah login
            $product_quantity = query("SELECT cart_quantity FROM cart WHERE product_id = $product_id")[0]["cart_quantity"];
            if ($product_quantity == 1) {
                echo "<script>
                            alert('Jumlah Barang Tidak Boleh 0 !');
                            document.location.href = 'everywhere.php?key=quantity';
                        </script>";
                exit;
            }

            if ($_SESSION["cart"][$product_id] == 1) {
                echo "<script>
                        alert('Jumlah Barang Tidak Boleh 0 !');
                        document.location.href = 'everywhere.php?key=quantity';
                    </script>";
                exit;
            }

            // query update untuk mengupdate kuantitas dan total harga produk di db
            mysqli_query($conn, "UPDATE cart SET 
                                    cart_quantity = cart_quantity - 1,
                                    cart_totalprice = cart_quantity * cart_price
                                WHERE product_id = $product_id");
        }

        // mereset post
        header("Location: everywhere.php?key=quantity");
    }
}

// function untuk menambahkan barang di cart ke db (barang belum pernah ada di db)
function AddCartToDB($cart)
{
    global $conn;

    // INSERT KE DATABASE

    // ambil id produk di session 
    // $_SESSION = [ "cart" => [ 1 => 5, 2 => 8 ], "login" => [...] ])
    $array_keys = array_keys($cart);

    // ambil id user di session
    $user_id = $_SESSION["login"]["user_id"];

    // melakukan perulangan untuk memasukkan produk di cart ke db
    foreach ($array_keys as $product_id) {
        // mengambil kuantitas dari cart berdasarkan id produk
        $quantity = $cart[$product_id];

        // melakukan query ke db produk untuk mendapatkan harga produk
        $product_price = query("SELECT product_price FROM product WHERE product_id = $product_id")[0]["product_price"];

        // mengambil kuantitas dari cart berdasarkan id produk
        $totalprice = $product_price * $quantity;

        mysqli_query($conn, "INSERT INTO cart 
                                    VALUES(null, $user_id, $product_id, $quantity, $product_price, $totalprice)
                                    ");
    }

    // mereset isi dari cart
    unset($_SESSION["cart"]);
}

function AddCartToDBFiltered($cart)
{
    global $conn;

    // INSERT KE DATABASE (Filter)

    // ambil id produk di session 
    // $_SESSION = [ "cart" => [ 1 => 5, 2 => 8 ], "login" => [...] ])
    $array_keys = array_keys($cart);

    // ambil id user di session
    $user_id = $_SESSION["login"]["user_id"];

    // melakukan perulangan untuk memasukkan produk di cart ke db
    foreach ($array_keys as $product_id) {
        // mengambil kuantitas dari cart berdasarkan id produk
        $quantity = $cart[$product_id];

        // melakukan query ke db produk untuk mendapatkan harga produk
        $product_price = query("SELECT product_price FROM product WHERE product_id = $product_id")[0]["product_price"];

        // mengalikan kuantitas dengan harga produk
        $totalprice = $product_price * $quantity;

        // melakukan query ke db untuk memastikan bahwa produk sudah ada atau belum di db
        $cart_product = query("SELECT * FROM cart WHERE product_id = $product_id");

        // jika produk sudah ada di db (filter)
        if (count($cart_product) == 1) {
            // kuantitas dan total harga ditambahkan dengan data didalam tabel (hasil query)
            $quantity += $cart_product[0]["cart_quantity"];
            $totalprice += $cart_product[0]["cart_totalprice"];

            // query update untuk mengupdate kuantitas dan total harga produk
            mysqli_query($conn, "UPDATE cart SET
                                    cart_quantity = $quantity,
                                    cart_totalprice = $totalprice
                                WHERE product_id = $product_id
                                ");
        } else {
            // jika produk belum ada di db

            // query insert untuk memasukkan data ke db
            mysqli_query($conn, "INSERT INTO cart 
                                VALUES(null, $user_id, $product_id, $quantity, $product_price, $totalprice)
                                ");
        }
    }

    // mereset isi dari cart
    unset($_SESSION["cart"]);
}
