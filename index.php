<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION["role"] === "admin") {
    header("Location: admin/dashboard.php");
    exit();
}

if ($_SESSION["role"] === "student") {
    header("Location: student/dashboard.php");
    exit();
}

// If role is invalid
session_unset();
session_destroy();

header("Location: login.php");
exit();

?>