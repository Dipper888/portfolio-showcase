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
    // Count students
    $sql = "SELECT COUNT(*) AS total_students
            FROM users
            WHERE role = 'student'";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $totalStudents = $row["total_students"];
?>