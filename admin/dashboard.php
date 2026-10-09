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
