<?php
session_start();

require_once "includes/db.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check if user is admin
if ($_SESSION["role"] != "admin") {
    header("Location: student/dashboard.php");
    exit();
}

// Check project ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid file request.");
}

$projectId = (int) $_GET["id"];

// Get file path from database
$sql = "SELECT file_path FROM projects WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$projectId);
$stmt->execute();

$result = $stmt->get_result();

// Check if project exists
if ($result->num_rows != 1) {
    die("File not found.");
}

$project = $result->fetch_assoc();

$fileName = basename($project["file_path"]);
$filePath = __DIR__ . "/uploads/" . $fileName;

// Check if file exists
if (!file_exists($filePath)) {
    die("File not found.");
}

// Download file
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . $fileName . "\"");
header("Content-Length: " . filesize($filePath));

readfile($filePath);
exit();
?>