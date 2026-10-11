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

                <tbody>
                    <?php if ($projects->num_rows > 0): ?>
                        <?php while ($row = $projects->fetch_assoc()): ?>
                            
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($row["title"]); ?></td>

                                <td>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($row["category_name"]); ?></span>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row["tech_stack"]); ?>
                                </td>

                                <td>
                                    <!-- Show the download link only if a file was uploaded; download.php handles the request. -->
                                    <?php if (!empty($row["file_path"])): ?>
                                        <a href="../download.php?project_id=<?php echo $row["id"]; ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No File</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php echo date("d M Y", strtotime($row["created_at"])); ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>
                        
                        <tr>
                            <td colspan="5" class="text-center text-muted">No portfolio submissions in here.</td>
                        </tr>
                    
                        <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>