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