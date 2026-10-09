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

    // Count students
    $sql = "SELECT COUNT(*) AS total_students
            FROM users
            WHERE role = 'student'";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $totalStudents = $row["total_students"];


    // Count categories
    $sql = "SELECT COUNT(*) AS total_categories
            FROM categories";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $totalCategories = $row["total_categories"];

    // Count submissions
    $sql = "SELECT COUNT(*) AS total_submissions
            FROM projects";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $totalSubmissions = $row["total_submissions"];

    // Get recent submissions
    $sql = "SELECT projects.title,projects.created_at,users.full_name,categories.category_name
            FROM projects
            JOIN users ON projects.user_id = users.id
            JOIN categories ON projects.category_id = categories.id
            ORDER BY projects.created_at DESC
            LIMIT 5";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $recentSubmissions = $stmt->get_result();
    require_once "../includes/header.php";
?>

<h2>Admin Overview</h2>

<p>Lecturer & Coordinator Overview</p>

<div class="row mt-4">

    <!-- Students -->
    <div class="col-md-4 mb-3">

        <div class="card">

            <div class="card-body">

                <h5>Students</h5>

                <h2>
                    <?php echo $totalStudents; ?>
                </h2>

            </div>

        </div>

    </div>
     <!-- Categories -->
    <div class="col-md-4 mb-3">

        <div class="card">

            <div class="card-body">

                <h5>Categories</h5>

                <h2>
                    <?php echo $totalCategories; ?>
                </h2>

            </div>

        </div>

    </div>
    <!-- Submissions -->
    <div class="col-md-4 mb-3">

        <div class="card">

            <div class="card-body">

                <h5>Submissions</h5>

                <h2>
                    <?php echo $totalSubmissions; ?>
                </h2>

            </div>

        </div>

    </div>

</div>
<h3 class="mt-4">Recent Submissions</h3>

<div class="table-responsive">

    <table class="table table-bordered">

        <thead>

            <tr>
                <th>Student Name</th>
                <th>Project Title</th>
                <th>Category</th>
                <th>Date</th>
            </tr>

        </thead>

        <tbody>

            <?php while ($project = $recentSubmissions->fetch_assoc()) { ?>

                <tr>

                    <td>
                        <?php echo htmlspecialchars($project["full_name"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($project["title"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($project["category_name"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($project["created_at"]); ?>
                    </td>

                </tr>

            <?php } ?>

        </tbody>

    </table>

</div>

<?php require_once "../includes/footer.php"; ?>
