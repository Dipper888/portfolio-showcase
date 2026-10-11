<?php
$required_role = "student";

require_once "../includes/header.php";
require_once "../includes/db.php";

$userId = $_SESSION["user_id"];

// Retrieve only the logged-in student's submissions.
$sql = "SELECT projects.id, projects.title, projects.tech_stack, projects.file_path, projects.created_at, categories.category_name
        FROM projects
        JOIN categories ON projects.category_id = categories.id
        WHERE projects.user_id = ?
        ORDER BY projects.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$projects = $stmt->get_result();
?>