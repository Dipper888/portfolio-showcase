<?php
session_start();

require_once "../includes/db.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit();

}

// Check if user is admin
if ($_SESSION["role"] != "admin") {

    header("Location: ../student/dashboard.php");
    exit();

}

// Get all submissions using SQL JOIN
$sql = "SELECT projects.id,
               projects.title,
               projects.description,
               projects.file_name,
               projects.created_at,
               users.username,
               categories.category_name
        FROM projects
        JOIN users ON projects.user_id = users.id
        JOIN categories ON projects.category_id = categories.id
        ORDER BY projects.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$submissions = $stmt->get_result();

require_once "../includes/header.php";
?>

<h2>Student Submissions</h2>
<p>View all submitted student projects.</p>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Project Title</th>
                <th>Category</th>
                <th>Description</th>
                <th>File</th>
                <th>Date Submitted</th>
            </tr>
        </thead>
        <tbody>

            <?php if ($submissions->num_rows > 0) { ?>

                <?php while ($submission = $submissions->fetch_assoc()) { ?>

                    <tr>
                        <td><?php echo $submission["id"]; ?></td>
                        <td><?php echo htmlspecialchars($submission["username"]); ?></td>
                        <td><?php echo htmlspecialchars($submission["title"]); ?></td>
                        <td><?php echo htmlspecialchars($submission["category_name"]); ?></td>
                        <td><?php echo htmlspecialchars($submission["description"]); ?></td>
                        <td>
                            <a href="../download.php?id=<?php echo $submission["id"]; ?>" class="btn btn-success btn-sm">
                                Download
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($submission["created_at"]); ?></td>
                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="7" class="text-center">No submissions found.</td>
                </tr>

            <?php } ?>

        </tbody>
    </table>
</div>

<?php require_once "../includes/footer.php"; ?>