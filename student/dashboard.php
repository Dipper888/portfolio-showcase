<?php
// Handle AJAX searches separately while checking student access.
$isAjax = isset($_GET["ajax"]) && $_GET["ajax"] == "1";

if ($isAjax)
{
    session_start();

    if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") != "student")
    {
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

<?php if (!$isAjax): ?>

    <?php if (isset($_SESSION["login_success"])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i>
            Welcome back, <?php echo htmlspecialchars($_SESSION["full_name"]); ?>!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION["login_success"]); ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">
                <i class="bi bi-grid-fill text-primary"></i> Project Showcase Directory
            </h2>
            <p class="text-muted">
                Explore student portfolios and Final Year Projects.
            </p>
        </div>

        <a href="submit_project.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Submit New Project
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="keyword" class="form-control"
                       placeholder="Search by project title, tech stack, or student name...">
            </div>
        </div>
    </div>

    <div class="row g-4 project-results">

<?php endif; ?>