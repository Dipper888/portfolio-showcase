<?php
session_start();

require_once "../includes/db.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit();

}

// Check if user is admin
if ($_SESSION["role"] != "admin") {

    header("Location: ../student/dashboard.php");
    exit();

}
?>