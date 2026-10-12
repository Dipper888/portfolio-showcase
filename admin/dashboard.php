<?php
// Restrict this page to admins using the role check in header.php.
$required_role = "admin";

require_once "../includes/header.php";
require_once "../includes/db.php";

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE role = 'student'");
$stmt->execute();
$studentCount = $stmt->get_result()->fetch_assoc()["total"];

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM categories");
$stmt->execute();
$categoryCount = $stmt->get_result()->fetch_assoc()["total"];

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM projects");
$stmt->execute();
$projectCount = $stmt->get_result()->fetch_assoc()["total"];

// Retrieve recent submissions using SQL JOIN.
$sql = "SELECT users.full_name, projects.title, categories.category_name, projects.created_at
        FROM projects
        JOIN users ON projects.user_id = users.id
        JOIN categories ON projects.category_id = categories.id
        ORDER BY projects.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$recentProjects = $stmt->get_result();
?>


<?php if (isset($_SESSION["login_success"])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill"></i>
        Welcome back, <?php echo htmlspecialchars($_SESSION["full_name"]); ?>!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION["login_success"]); ?>
<?php endif; ?>

<h2 class="fw-bold"><i class="bi bi-speedometer2 text-primary"></i> Admin Dashboard</h2>
<p class="text-muted">Manage student portfolios and project submissions.</p>

<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="card border-start border-primary border-4 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Students</p>
                    <h2 class="fw-bold mb-0"><?php echo $studentCount; ?></h2>
                </div>
                <i class="bi bi-people-fill text-primary fs-2"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-start border-success border-4 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Categories</p>
                    <h2 class="fw-bold mb-0"><?php echo $categoryCount; ?></h2>
                </div>
                <i class="bi bi-collection-fill text-success fs-2"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-start border-info border-4 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Submissions</p>
                    <h2 class="fw-bold mb-0"><?php echo $projectCount; ?></h2>
                </div>
                <i class="bi bi-folder-fill text-info fs-2"></i>
            </div>
        </div>
    </div>

</div>

<div class="card shadow-sm">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Recent Submissions</h4>
            <a href="submissions.php" class="btn btn-outline-primary btn-sm">View All</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Student Name</th>
                        <th>Project Title</th>
                        <th>Category</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($recentProjects->num_rows > 0): ?>
                        <?php while ($row = $recentProjects->fetch_assoc()): ?>
                            
                            <tr>
                                <td><?php echo htmlspecialchars($row["full_name"]); ?></td>
                                <td><?php echo htmlspecialchars($row["title"]); ?></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?php echo htmlspecialchars($row["category_name"]); ?>
                                    </span>
                                </td>
                                <td><?php echo date("d M Y", strtotime($row["created_at"])); ?></td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>
                        
                        <tr>
                            <td colspan="4" class="text-center text-muted">No submissions available.</td>
                        </tr>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include "../includes/footer.php"; ?>