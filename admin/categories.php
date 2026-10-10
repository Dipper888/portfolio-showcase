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
    
    // Get all categories
        $sql = "SELECT id, category_name, description, created_at FROM categories ORDER BY id DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $categories = $stmt->get_result();
}
require_once "../includes/header.php";
?>

    <h2>Manage Categories</h2>

    <p>Create and manage project categories.</p>


    <!-- Error message -->
    <?php if ($error != "") { ?>

        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php } ?>

    <!-- Success message -->
    <?php if ($success != "") { ?>

        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>

    <?php } ?>
    <h4 class="mt-4">Create Category</h4>

    <form method="POST">

        <!-- Category Name -->
        <div class="mb-3">

            <label class="form-label">Category Name</label>
            <input type="text" name="category_name" class="form-control" required>

        </div>
        <!-- Description -->
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>

        </div>
        <button type="submit" class="btn btn-primary">Create Category</button>

    </form>