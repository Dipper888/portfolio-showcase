<?php

session_start();
require_once "includes/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Check empty fields
    if ($email === "" || $password === "") {
        $error = "Please enter email and password.";
    } else {

        // Find user using email
        $stmt = $conn->prepare(
            "SELECT id, full_name, email, password, role
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            // Verify hashed password
            if (password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION["role"] = $user["role"];

                // Redirect based on role
                if ($user["role"] === "admin") {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: student/dashboard.php");
                }

                exit();

            } else {
                $error = "Invalid email or password.";
            }

        } else {
            $error = "Invalid email or password.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

<div class="container mt-5">

    <div class="col-md-6 mx-auto auth-card">

        <h2 class="mb-4">Login</h2>

        <?php if ($error !== ""): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">

            <div class="mb-3">
                <label class="form-label">Email</label>

                <input type="email"
                       name="email"
                       id="email"
                       class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>

                <input type="password"
                       name="password"
                       id="password"
                       class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">
                Login
            </button>

        </form>

        <p class="mt-3">
            Don't have an account?
            <a href="register.php">Register here</a>
        </p>

    </div>

</div>

</body>
</html>