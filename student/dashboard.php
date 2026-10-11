<?php
// Handle AJAX searches separately while checking student access.
$isAjax = isset($_GET["ajax"]) && $_GET["ajax"] == "1";

if ($isAjax)
{
    session_start();

    if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") != "student")
    {
        http_response_code(403);
        exit("Access denied.");
    }
}
else
{
    $required_role = "student";
    require_once "../includes/header.php";
}

require_once "../includes/db.php";

$keyword = trim($_GET["keyword"] ?? "");
$search = "%" . $keyword . "%";

// Retrieve projects and search results using one SQL query.
$sql = "SELECT projects.id, projects.user_id, projects.title,
        projects.description, projects.tech_stack, projects.file_path,
        projects.created_at, users.full_name, categories.category_name
        FROM projects
        JOIN users ON projects.user_id = users.id
        JOIN categories ON projects.category_id = categories.id
        WHERE projects.title LIKE ?
        OR projects.tech_stack LIKE ?
        OR users.full_name LIKE ?
        ORDER BY projects.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $search, $search, $search);
$stmt->execute();
$projects = $stmt->get_result();
?>