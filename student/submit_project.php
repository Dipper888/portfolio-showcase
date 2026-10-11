<?php
$required_role = "student";

require_once "../includes/db.php";
require_once "../includes/header.php";

$message = "";
$alertType = "danger";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $title = trim($_POST["title"] ?? "");
    $categoryId = filter_input(INPUT_POST, "category_id", FILTER_VALIDATE_INT);
    $techStack = trim($_POST["tech_stack"] ?? "");
    $description = trim($_POST["description"] ?? "");

    // Validate required fields before processing the upload.
    if ($title != "" && $techStack != "" && $description != "" &&
        $categoryId && $categoryId > 0 &&
        isset($_FILES["document"]) && $_FILES["document"]["error"] != UPLOAD_ERR_NO_FILE)
    {
        if (strlen($title) > 200 || strlen($techStack) > 255)
        {
            $message = "Project title or tech stack is too long.";
        }
        elseif ($_FILES["document"]["error"] != UPLOAD_ERR_OK)
        {
            $message = "Please upload a valid file.";
        }
        elseif ($_FILES["document"]["size"] == 0 || $_FILES["document"]["size"] > 5 * 1024 * 1024)
        {
            $message = "File must not be empty or exceed 5 MB.";
        }
        else
        {
            // Validate both the file extension and MIME type for upload security.
            $extension = strtolower(pathinfo($_FILES["document"]["name"], PATHINFO_EXTENSION));
            $mimeType = mime_content_type($_FILES["document"]["tmp_name"]);

            $allowedTypes = [
                "pdf" => ["application/pdf"],
                "docx" => ["application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/zip", "application/x-zip-compressed"],
                "txt" => ["text/plain"],
                "zip" => ["application/zip", "application/x-zip-compressed"]];

            if (!isset($allowedTypes[$extension]) || !in_array($mimeType, $allowedTypes[$extension]))
            {
                $message = "Only PDF, DOCX, TXT or ZIP files are allowed.";
            }
            else
            {
                $stmt = $conn->prepare("SELECT id FROM categories WHERE id = ?");
                $stmt->bind_param("i", $categoryId);
                $stmt->execute();
                $category = $stmt->get_result()->fetch_assoc();

                if (!$category)
                {
                    $message = "Selected category does not exist.";
                }
                else
                {
                    // Generate a unique file name and store the uploaded file in the uploads folder.
                    $fileName = uniqid("project_") . "." . $extension;
                    $filePath = "uploads/" . $fileName;
                    $destination = __DIR__ . "/../uploads/" . $fileName;

                    if (move_uploaded_file($_FILES["document"]["tmp_name"], $destination))
                    {
                        $userId = $_SESSION["user_id"];

                        // Save the project details, student ID, and uploaded file path using a prepared statement.
                        $stmt = $conn->prepare("INSERT INTO projects (user_id, category_id, title, description, tech_stack, file_path) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("iissss", $userId, $categoryId, $title, $description, $techStack, $filePath);

                        if ($stmt->execute())
                        {
                            $message = "Project submitted successfully!";
                            $alertType = "success";
                        }
                        else
                        {
                            $message = "Failed to save project to the database.";
                        }
                    }
                }
            }
        }
    }
}

$stmt = $conn->prepare("SELECT id, category_name FROM categories ORDER BY category_name");
$stmt->execute();
$categories = $stmt->get_result();
?>

<div class="row justify-content-center">
    <div class="col-md-9 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">

                <h3 class="text-primary mb-4"><i class="bi bi-cloud-arrow-up-fill"></i> Submit Portfolio Project</h3>

                <?php if ($message != ""): ?>
                    <div class="alert alert-<?php echo $alertType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="submit_project.php" enctype="multipart/form-data" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Project Title</label>
                        <input type="text" name="title" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- Select Category --</option>

                            <?php while ($row = $categories->fetch_assoc()): ?>
                                <option value="<?php echo $row["id"]; ?>">
                                    <?php echo htmlspecialchars($row["category_name"]); ?>
                                </option>
                            <?php endwhile; ?>

                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tech Stack (Comma Separated)</label>
                        <input type="text" name="tech_stack" class="form-control" placeholder="e.g. PHP, MySQL, Bootstrap 5, AJAX">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project Description</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Documentation (PDF, DOCX, TXT or ZIP - Max 5 MB)</label>
                        <input type="file" name="document" class="form-control" accept=".pdf,.docx,.txt,.zip">
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill"></i> Submit Project</button>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="../validation.js"></script>

<?php include "../includes/footer.php"; ?>