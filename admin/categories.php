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

// Message variables
$error = "";
$success = "";

// Check when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $categoryName = trim($_POST["category_name"]);
    $description = trim($_POST["description"]);

    // Check empty fields
    if (empty($categoryName) || empty($description)) {

        $error = "Please fill in all fields.";

    } else {

        // Insert category
        $sql = "INSERT INTO categories (category_name, description)
                VALUES (?, ?)";

        $stmt = $conn->prepare($sql);

        // Bind values
        $stmt->bind_param(
            "ss",
            $categoryName,
            $description
        );

        // Execute statement
        if ($stmt->execute()) {

            $success = "Category created successfully.";

        } else {

            $error = "Failed to create category.";

        }

        $stmt->close();
    }
}

?>