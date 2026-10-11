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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">
        <i class="bi bi-briefcase-fill text-primary"></i> My Portfolio Submissions
    </h2>

    <a href="submit_project.php" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Submission
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Project Title</th>
                        <th>Category</th>
                        <th>Tech Stack</th>
                        <th>File</th>
                        <th>Date</th>
                    </tr>
                </thead>