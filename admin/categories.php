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

// Create category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["category_name"])) {

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
        $stmt->bind_param("ss",$categoryName,$description);

        // Execute statement
        if ($stmt->execute()) {
            $success = "Category created successfully.";
        } else {
            $error = "Failed to create category.";
        }

        $stmt->close();
    }
}


    // Delete category
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_id"])) {
        $deleteId = $_POST["delete_id"];
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i",$deleteId);

        if ($stmt->execute()) {
            $success = "Category deleted successfully.";
        } else {
            $error = "Failed to delete category.";
        }
        $stmt->close();
    }
        // Get all categories
        $sql = "SELECT id, category_name, description, created_at FROM categories ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $categories = $stmt->get_result();

        require_once "../includes/header.php";
?>
        <h2>Manage Categories</h2>
        <p>Create and manage project categories.</p>

        <!-- Create Category -->
        <h4 class="mt-4">Create Category</h4>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Category Name</label>
                <input type="text" name="category_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Create Category</button>

        </form>

        <!-- Existing  Category -->
        <h4 class="mt-5">Existing Categories</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Date Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php while ($category = $categories->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $category["id"]; ?></td>
                            <td><?php echo htmlspecialchars($category["category_name"]); ?></td>
                            <td><?php echo htmlspecialchars($category["description"]); ?></td>
                            <td><?php echo htmlspecialchars($category["created_at"]); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="delete_id" value="<?php echo $category["id"]; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                                </form>
                            </td>
                        </tr>

                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php require_once "../includes/footer.php"; ?>