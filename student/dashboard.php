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

<?php if ($projects->num_rows > 0): ?>
    <?php while ($row = $projects->fetch_assoc()): ?>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">

                    <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                        <span class="badge bg-primary text-wrap text-start">
                            <?php echo htmlspecialchars($row["category_name"]); ?>
                        </span>

                        <small class="text-muted text-nowrap">
                            <i class="bi bi-clock"></i>
                            <?php echo date("d M Y", strtotime($row["created_at"])); ?>
                        </small>
                    </div>

                    <h5 class="fw-bold">
                        <?php echo htmlspecialchars($row["title"]); ?>
                    </h5>

                    <p class="text-muted">
                        <?php echo htmlspecialchars($row["description"]); ?>
                    </p>

                    <div class="mb-3">
                        <strong>Tech Stack:</strong>
                        <div class="mt-2">
                            <span class="badge bg-secondary">
                                <?php echo htmlspecialchars($row["tech_stack"]); ?>
                            </span>
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-auto d-flex justify-content-between align-items-center">

                        <small>
                            <i class="bi bi-person-fill"></i>
                            <?php echo htmlspecialchars($row["full_name"]); ?>
                        </small>

                        <!-- Show the report download link only for the student's own uploaded files. -->
                        <?php if ($row["user_id"] == $_SESSION["user_id"] && !empty($row["file_path"])): ?>
                            <a href="../download.php?project_id=<?php echo $row["id"]; ?>"
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-download"></i> Report
                            </a>
                        <?php endif; ?>

                    </div>

                </div>
            </div>
        </div>

    <?php endwhile; ?>

<?php else: ?>

    <div class="col-12">
        <div class="alert alert-info text-center">
            <?php if ($keyword == ""): ?>
                No portfolio projects in here.
            <?php else: ?>
                No matching projects found.
            <?php endif; ?>
        </div>
    </div>

<?php endif; ?>

<?php if (!$isAjax): ?>

    </div>

    <script src="../validation.js"></script>

    <?php include "../includes/footer.php"; ?>

<?php endif; ?>