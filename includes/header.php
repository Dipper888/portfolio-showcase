<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}

// Check user sessions and roles to prevent unauthorized page access.
$path = isset($required_role) ? "../" : "";

if (isset($required_role)) 
{
    if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"]))
    {
        header("Location: " . $path . "login.php");
        exit();
    }

    if ($_SESSION["role"] != $required_role) 
    {
        header("Location: " . $path . "index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Showcase</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo $path; ?>styles.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid px-4">

        <a class="navbar-brand fw-bold" href="<?php echo $path; ?>index.php">
            <i class="bi bi-mortarboard-fill text-white"></i>
            Portfolio Showcase
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto">

                <!-- Display navigation bar based on the logged-in user's role. -->
                <?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "student"): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $path; ?>student/dashboard.php">
                            <i class="bi bi-house-door-fill"></i> Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $path; ?>student/submit_project.php">
                            <i class="bi bi-upload"></i> Submit Project
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $path; ?>student/my_projects.php">
                            <i class="bi bi-briefcase-fill"></i> My Portfolio
                        </a>
                    </li>

                <?php elseif (isset($_SESSION["role"]) && $_SESSION["role"] == "admin"): ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $path; ?>admin/dashboard.php">
                            <i class="bi bi-graph-up-arrow"></i> Admin Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $path; ?>admin/categories.php">
                            <i class="bi bi-folder-plus"></i> Categories
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $path; ?>admin/submissions.php">
                            <i class="bi bi-list-check"></i> All Submissions
                        </a>
                    </li>
                <?php endif; ?>

            </ul>

            <ul class="navbar-nav ms-lg-2">

                <?php if (isset($_SESSION["user_id"])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle text-info"></i>
                            <?php echo htmlspecialchars($_SESSION["full_name"]); ?>
                            (<?php echo ucfirst($_SESSION["role"]); ?>)
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?php echo $path; ?>logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $path; ?>login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $path; ?>register.php">
                            <i class="bi bi-person-plus-fill"></i> Register
                        </a>
                    </li>
                <?php endif; ?>

            </ul>

        </div>
    </div>
</nav>

<main class="my-4 px-4">