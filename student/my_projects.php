<?php
$required_role = "student";
require_once "../includes/header.php";
require_once "../includes/db.php";

$userId = $_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["action"] ?? "") == "delete")
{
    $projectId = filter_input(INPUT_POST, "project_id", FILTER_VALIDATE_INT);

    if ($projectId)
    {
        // Only delete a project that belongs to the logged-in student.
        $stmt = $conn->prepare("SELECT file_path FROM projects WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $projectId, $userId);
        $stmt->execute();
        $project = $stmt->get_result()->fetch_assoc();

        if ($project)
        {
            $stmt = $conn->prepare("DELETE FROM projects WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $projectId, $userId);
            $stmt->execute();

            if ($stmt->affected_rows > 0)
            {
                // Remove the uploaded file after deleting the project record.
                if (!empty($project["file_path"]))
                {
                    $filePath = __DIR__ . "/../uploads/" . basename($project["file_path"]);
                    if (is_file($filePath))
                    {
                        unlink($filePath);
                    }
                }

                $message = "Project deleted successfully!";
            }
        }
    }
}

$sql = "SELECT projects.id, projects.title, projects.tech_stack, projects.file_path,
        projects.created_at, categories.category_name
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

<?php if ($message != ""): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

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
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($projects->num_rows > 0): ?>
                        <?php while ($row = $projects->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold">
                                    <?php echo htmlspecialchars($row["title"]); ?>
                                </td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row["category_name"]); ?></span></td>
                                <td><?php echo htmlspecialchars($row["tech_stack"]); ?></td>
                                <td>
                                    <?php if (!empty($row["file_path"])): ?>
                                        <a href="../download.php?project_id=<?php echo $row["id"]; ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No File</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date("d M Y", strtotime($row["created_at"])); ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="submit_project.php?edit=<?php echo $row["id"]; ?>" class="btn btn-warning btn-sm" title="Edit project">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form method="POST" action="my_projects.php" onsubmit="return confirm('Delete this project and its uploaded file?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="project_id" value="<?php echo $row["id"]; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete project">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">No portfolio submissions in here.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>