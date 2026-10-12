<?php
$required_role = "admin";

require_once "../includes/db.php";
require_once "../includes/header.php";

// Retrieve all student submissions using SQL JOIN.
$sql = "SELECT projects.id, users.full_name, users.email,
        projects.title, categories.category_name,
        projects.tech_stack, projects.file_path, projects.created_at
        FROM projects
        JOIN users ON projects.user_id = users.id
        JOIN categories ON projects.category_id = categories.id
        ORDER BY projects.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$submissions = $stmt->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="bi bi-list-check text-primary"></i> All Student Submissions</h2>

    <span class="badge bg-primary">Total: <?php echo $submissions->num_rows; ?> Projects</span>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>Student Name</th>
                        <th>Project Title</th>
                        <th>Category</th>
                        <th>Tech Stack</th>
                        <th>Submitted File</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($submissions->num_rows > 0): ?>
                        <?php while ($row = $submissions->fetch_assoc()): ?>

                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($row["full_name"]); ?></strong>
                                    <div class="text-muted small"><?php echo htmlspecialchars($row["email"]); ?></div>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row["title"]); ?>
                                </td>

                                <td>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($row["category_name"]); ?></span>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row["tech_stack"]); ?>
                                </td>

                                <td>
                                    <!-- Show the download link when a file path exists; download.php handles the request. -->
                                    <?php if ($row["file_path"] != ""): ?>
                                        <a href="../download.php?project_id=<?php echo $row["id"]; ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No File</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php echo date("d M Y, h:i A", strtotime($row["created_at"])); ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr><td colspan="6" class="text-center text-muted">No submissions in here.</td></tr>

                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>