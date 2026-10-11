<?php
$prefix = "";

if (strpos($_SERVER["PHP_SELF"], "/admin/") !== false ||
    strpos($_SERVER["PHP_SELF"], "/student/") !== false) {
    $prefix = "../";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Portfolio Showcase</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- External CSS -->
    <link rel="stylesheet" href="<?php echo $prefix; ?>css/styles.css">
</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container">

        <a class="navbar-brand" href="<?php echo $prefix; ?>index.php"> Portfolio Showcase</a>

        <div>

            <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] == "admin") { ?>
                <a href="<?php echo $prefix; ?>admin/dashboard.php" class="btn btn-light btn-sm">Dashboard</a>
                <a href="<?php echo $prefix; ?>admin/categories.php" class="btn btn-light btn-sm">Categories</a>
                <a href="<?php echo $prefix; ?>admin/submissions.php" class="btn btn-light btn-sm">All Submissions</a>
                <span class="text-white ms-3">
                    Admin
                </span>

                <a href="<?php echo $prefix; ?>logout.php" class="btn btn-danger btn-sm ms-2">Logout</a>

            <?php } elseif (isset($_SESSION["user_id"]) && $_SESSION["role"] == "student") { ?>

                <a href="<?php echo $prefix; ?>student/dashboard.php" class="btn btn-light btn-sm">Dashboard</a>
                <a href="<?php echo $prefix; ?>student/submit_project.php" class="btn btn-light btn-sm">Submit Project</a>
                <a href="<?php echo $prefix; ?>student/my_projects.php" class="btn btn-light btn-sm">My Projects</a>

                <span class="text-white ms-3">
                    Student
                </span>

                <a href="<?php echo $prefix; ?>logout.php" class="btn btn-danger btn-sm ms-2">Logout</a>

            <?php } else { ?>

                <a href="<?php echo $prefix; ?>login.php" class="btn btn-light btn-sm">Login</a>
                <a href="<?php echo $prefix; ?>register.php" class="btn btn-primary btn-sm">Register</a>

            <?php } ?>

        </div>

    </div>

</nav>

<div class="container mt-4">