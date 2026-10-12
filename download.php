<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"]))
{
    header("Location: login.php");
    exit();
}

$projectId = filter_input(INPUT_GET, "project_id", FILTER_VALIDATE_INT);

if (!$projectId || $projectId < 1)
{
    die("Invalid project ID.");
}

$stmt = $conn->prepare("SELECT user_id, file_path FROM projects WHERE id = ?");
$stmt->bind_param("i", $projectId);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

if (!$project || empty($project["file_path"]))
{
    die("File not found.");
}

// Allow Admin or the Student who owns the project.
if ($_SESSION["role"] != "admin" && 
    ($_SESSION["role"] != "student" || $_SESSION["user_id"] != $project["user_id"]))
{
    die("Access denied.");
}

// Use only the file name to keep downloads inside the uploads folder.
$fileName = basename($project["file_path"]);
$filePath = __DIR__ . "/uploads/" . $fileName;

if (!is_file($filePath))
{
    die("Uploaded file is missing from the server.");
}

// Tell the browser to download the file, then send its contents.
header('Content-Disposition: attachment; filename="' . $fileName . '"');

readfile($filePath);
?>