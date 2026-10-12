<?php
$required_role = "admin";
require_once "../includes/header.php";
require_once "../includes/db.php";

$message = "";
$error = "";
$editId = $_GET["edit"] ?? "";
$editing = null;

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $categoryId = $_POST["category_id"] ?? "";

    if (isset($_POST["save"]))
    {
        $category_name = trim($_POST["category_name"] ?? "");
        $description = trim($_POST["description"] ?? "");

        // PHP also checks the fields before saving to MySQL.
        if ($category_name != "" && $description != "" && strlen($category_name) <= 100 &&
            ($categoryId == "" || ctype_digit($categoryId)))
        {
            $stmt = $conn->prepare("SELECT id FROM categories WHERE category_name = ?");
            $stmt->bind_param("s", $category_name);
            $stmt->execute();
            $existing = $stmt->get_result()->fetch_assoc();

            if (!$existing || $existing["id"] == $categoryId)
            {
                if ($categoryId == "")
                {
                    $stmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
                    $stmt->bind_param("ss", $category_name, $description);
                }
                else
                {
                    $stmt = $conn->prepare("UPDATE categories SET category_name = ?, description = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $category_name, $description, $categoryId);
                }

                if ($stmt->execute())
                {
                    $message = "Category saved successfully!";
                    $editId = "";
                }
            }
            else
            {
                $error = "A category with this name already exists.";
            }
        }
    }
    elseif (isset($_POST["delete"]) && ctype_digit($categoryId))
    {
        // Do not delete a category that is used by a project.
        $stmt = $conn->prepare("SELECT id FROM projects WHERE category_id = ? LIMIT 1");
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();

        if ($stmt->get_result()->fetch_assoc())
        {
            $error = "Cannot delete this category because it is used by an existing project.";
        }
        else
        {
            $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->bind_param("i", $categoryId);
            if ($stmt->execute() && $stmt->affected_rows > 0)
            {
                $message = "Category deleted successfully!";
                $editId = "";
            }
        }
    }
}

if ($editId != "" && ctype_digit($editId))
{
    $stmt = $conn->prepare("SELECT id, category_name, description FROM categories WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editing = $stmt->get_result()->fetch_assoc();
}

$categories = $conn->query("SELECT id, category_name, description FROM categories ORDER BY category_name");
?>

<h2 class="fw-bold mb-4"><i class="bi bi-folder-plus text-primary"></i> Category Management</h2>

<?php if ($message != ""): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($error != ""): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="text-primary mb-3">
                    <i class="bi bi-<?php echo $editing ? "pencil-square" : "plus-square-fill"; ?>"></i>
                    <?php echo $editing ? "Edit Category" : "Create Category"; ?>
                </h4>

                <form method="POST" action="categories.php<?php echo $editing ? "?edit=" . $editing["id"] : ""; ?>" novalidate>
                    <?php if ($editing): ?>
                        <input type="hidden" name="category_id" value="<?php echo $editing["id"]; ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="category_name" class="form-control" maxlength="100" value="<?php echo htmlspecialchars($editing["category_name"] ?? ""); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($editing["description"] ?? ""); ?></textarea>
                    </div>
                    <button type="submit" name="save" value="1" class="btn btn-primary w-100">
                        <?php echo $editing ? "Save Changes" : "Add Category"; ?>
                    </button>
                    <?php if ($editing): ?>
                        <a href="categories.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                    <?php endif; ?>
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
                            <tr><th>Category Name</th><th>Description</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($categories->num_rows > 0): ?>
                                <?php while ($row = $categories->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($row["category_name"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["description"]); ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="categories.php?edit=<?php echo $row["id"]; ?>" class="btn btn-warning btn-sm" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                <form method="POST" action="categories.php" onsubmit="return confirm('Delete this category?');">
                                                    <input type="hidden" name="category_id" value="<?php echo $row["id"]; ?>">
                                                    <button type="submit" name="delete" value="1" class="btn btn-danger btn-sm" title="Delete"><i class="bi bi-trash-fill"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center text-muted">No categories available.</td></tr>
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