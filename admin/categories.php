<?php
$required_role = "admin";

require_once "../includes/db.php";
require_once "../includes/header.php";

$message = "";
$alertType = "danger";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $category_name = trim($_POST["category_name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    // Server-side validation for category creation.
    if ($category_name != "" && $description != "")
    {
        if (strlen($category_name) > 100)
        {
            $message = "Category name must not exceed 100 characters.";
        }
        else
        {
            // Check for existing categories to prevent duplicate records.
            $stmt = $conn->prepare("SELECT id FROM categories WHERE category_name = ?");
            $stmt->bind_param("s", $category_name);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0)
            {
                $message = "Category already exists.";
            }
            else
            {
                // Insert the category using a prepared statement.
                $stmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
                $stmt->bind_param("ss", $category_name, $description);

                if ($stmt->execute())
                {
                    $message = "Category added successfully!";
                    $alertType = "success";
                }
                else
                {
                    $message = "Failed to add category.";
                }
            }
        }
    }
}

$stmt = $conn->prepare("SELECT category_name, description FROM categories ORDER BY category_name");
$stmt->execute();
$categories = $stmt->get_result();
?>

<h2 class="fw-bold mb-4"><i class="bi bi-folder-plus text-primary"></i> Category Management</h2>

<?php if ($message != ""): ?>
    <div class="alert alert-<?php echo $alertType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="row g-4">

    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body">

                <h4 class="text-primary mb-3">
                    <i class="bi bi-plus-square-fill"></i> Create Category
                </h4>

                <form method="POST" action="categories.php" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="category_name" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i> Add Category</button>
                </form>

            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body">

                <h4 class="mb-3">Existing Categories</h4>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>Category Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($categories->num_rows > 0): ?>
                                <?php while ($row = $categories->fetch_assoc()): ?>

                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($row["category_name"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["description"]); ?></td>
                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>
                            
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No categories available.</td>
                                </tr>

                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>

</div>

<script src="../validation.js"></script>

<?php include "../includes/footer.php"; ?>