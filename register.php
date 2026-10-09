<?php

require_once "includes/db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullName = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    // Check empty fields
    if ($fullName === "" || $email === "" || $password === "" || $confirmPassword === "") {
        $error = "Please complete all required fields.";
    }

    // Check email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email.";
    }

    // Check password confirmation
    elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    }

    else {

        // Check whether email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already registered.";
        } else {

            // Hash password before saving
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Registration is only for students
            $role = "student";

            $stmt = $conn->prepare(
                "INSERT INTO users (full_name, email, password, role)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $fullName,
                $email,
                $hashedPassword,
                $role
            );

            if ($stmt->execute()) {
                $success = "Registration successful.";
            } else {
                $error = "Registration failed.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

<div class="container mt-5">

    <div class="col-md-6 mx-auto">

        <h2 class="mb-4">Student Registration</h2>

        <?php if ($error !== ""): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success !== ""): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="registerForm">

            <div class="mb-3">
                <label class="form-label">Full Name</label>

                <input type="text"
                       name="full_name"
                       id="full_name"
                       class="form-control">
            </div>

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

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>

                <input type="password"
                       name="confirm_password"
                       id="confirm_password"
                       class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">
                Register
            </button>

        </form>

        <p class="mt-3">
            Already have an account?
            <a href="login.php">Login here</a>
        </p>

    </div>

</div>

</body>
</html>