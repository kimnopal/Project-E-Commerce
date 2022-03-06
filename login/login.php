<?php
// memulai session
session_start();

// menghubungkan ke functions
require '../functions.php';

// CEK COOKIE
if (isset($_COOKIE["id"]) && isset($_COOKIE["key"])) {
    $id = $_COOKIE["id"];
    $key = $_COOKIE["key"];
    // query data berdasarkan cookie
    $login_data = query("SELECT * FROM admin WHERE admin_id = $id");
    $login_data = query("SELECT * FROM admin WHERE admin_id = $id");

    // cek id
    if (mysqli_affected_rows($conn) === 1) {
        // cek key
        if ($key === hash('sha256', $login_data[0]["username"])) {
            $_SESSION["login"]["admin"] = true;
        }
    }

    $login_data = query("SELECT * FROM user WHERE user_id = $id");

    // cek id
    if (mysqli_affected_rows($conn) === 1) {
        // cek key
        if ($key === hash('sha256', $login_data[0]["user_username"])) {
            $_SESSION["login"]["user"] = true;
            $_SESSION["login"]["user_id"] = $login_data[0]["user_id"];
        }
    }
}

// CEK SESSION LOGIN
if (isset($_SESSION["login"]["admin"])) {
    if ($_SESSION["login"]["admin"]) {
        header("Location: ../dashboard/kategori/kategori.php");
        exit;
    }
}

if (isset($_SESSION["login"]["user"])) {
    if ($_SESSION["login"]["user"]) {
        header("Location: ../index.php");
        exit;
    }
}

// CEK APAKAH TOMBOL LOGIN DITEKAN
if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $error = false;

    $usernameStatus = cekUsername($username);

    // validasi admin
    if ($usernameStatus === "admin") {
        $adminData = query("SELECT * FROM admin WHERE username = '$username'")[0];
        if ($password === $adminData["password"]) {
            // cek cookie
            if (isset($_POST["remember"])) {
                setcookie('id', $adminData["admin_id"], time() + 3600, "/");
                setcookie('key', hash('sha256', $adminData["username"]), time() + 3600, "/");
            }

            $_SESSION["login"]["admin"] = true;
            header("Location: ../dashboard/kategori/kategori.php");
            exit;
        }
        $errorPassword = true;
    }

    // validasi user
    if ($usernameStatus === "user") {
        $userData = query("SELECT * FROM user WHERE user_username = '$username'")[0];
        if (password_verify($password, $userData["user_password"])) {
            // cek cookie
            if (isset($_POST["remember"])) {
                setcookie('id', $userData["user_id"], time() + 3600, "/");
                setcookie('key', hash('sha256', $userData["user_username"]), time() + 3600, "/");
            }

            $_SESSION["login"]["user"] = true;
            $_SESSION["login"]["user_id"] = $userData["user_id"];

            // cek session order
            if (isset($_SESSION["order"])) {
                if ($_SESSION["order"]) {
                    header("Location: ../order.php");
                    exit;
                }
            }

            header("Location: ../index.php");
            exit;
        }
        $errorPassword = true;
    }

    if (!isset($errorPassword)) {
        $errorUsername = true;
    }
}

if (isset($_POST["signup"])) {
    if (register($_POST) === "berhasil") {
        if (mysqli_affected_rows($conn) > 0) {
            echo "<script>
                    alert('Berhasil Mendaftar!');
                    document.location.href = 'login.php';
                </script>";
        } else {
            echo "<script>
                    alert('Gagal Mendaftar!');
                    document.location.href = 'login.php';
                </script>";
        }
    } else if (register($_POST) === "username") {
        echo "<script>
                alert('Username sudah terdaftar!');
                document.location.href = 'login.php';
            </script>";
        echo mysqli_error($conn);
    } else if (register($_POST) === "email") {
        echo "<script>
                alert('Email sudah terdaftar!');
                document.location.href = 'login.php';
            </script>";
        echo mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TokoCoba</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <form action="" method="post" class="sign-in-form">
                    <h2 class="title">Sign In</h2>
                    <?php if (isset($errorUsername)) : ?>
                        <div class="input-field error">
                            <i class="fas fa-user"></i>
                            <input type="text" placeholder="Username" name="username" autocomplete="off">
                        </div>
                        <p class="error-username error-input">Username yang Anda masukkan salah !</p>
                    <?php else : ?>
                        <div class="input-field">
                            <i class="fas fa-user"></i>
                            <input type="text" placeholder="Username" name="username" value="<?= isset($_POST["username"]) ? $username : ""; ?>" autocomplete="off">
                        </div>
                    <?php endif; ?>

                    <?php if (isset($errorPassword)) : ?>
                        <div class="input-field error">
                            <i class="fas fa-lock"></i>
                            <input type="password" placeholder="Password" name="password">
                        </div>
                        <p class="error-password error-input">Password yang Anda masukkan salah !</p>
                    <?php else : ?>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" placeholder="Password" name="password">
                        </div>
                    <?php endif; ?>
                    <div class="input-field remember">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn solid" name="login">Login</button>

                    <!-- <p class="social-text">Or Sign In with social platforms</p>
                    <div class="social-media">
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-google"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div> -->
                </form>

                <form action="" method="post" class="sign-up-form">
                    <h2 class="title">Sign up</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" placeholder="Username" name="user_username">
                    </div>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="text" placeholder="Email" name="user_email">
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" placeholder="Password" name="user_password">
                    </div>
                    <button type="submit" class="btn solid" name="signup">Sign up</button>

                    <!-- <p class="social-text">Or Sign up with social platforms</p>
                    <div class="social-media">
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-google"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div> -->
                </form>
            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>Belum Punya Akun ?</h3>
                    <p>Segera daftar sekarang juga.</p>
                    <button class="btn transparent" id="sign-up-btn">Sign up</button>
                </div>

                <img src="img/log.svg" class="image" alt="">
            </div>

            <div class="panel right-panel">
                <div class="content">
                    <h3>Sudah Punya Akun ?</h3>
                    <p>Silahkan masuk menggunakan akun anda.</p>
                    <button class="btn transparent" id="sign-in-btn">Sign up</button>
                </div>

                <img src="img/register.svg" class="image" alt="">
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>

</html>