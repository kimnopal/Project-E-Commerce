<?php
session_start();

if ($_SESSION["login"]["admin"]) {
    $admin = true;
}

$_SESSION = [];
session_unset();
session_destroy();

setcookie('id', '', time() - 9999, '/');
setcookie('key', '', time() - 9999, '/');

if (isset($admin)) {
    header("Location: login.php");
    exit;
}

header("Location: ../index.php");
exit;
