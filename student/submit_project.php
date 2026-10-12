<?php
session_start();
$required_role = "student";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") != "student")
{
    header("Location: ../login.php");
    exit();
}

require_once "../includes/db.php";

$userId = $_SESSION["user_id"];
$isEdit = isset($_GET["edit"]);
$editId = ($_GET["edit"] ?? 0);
$title = $techStack = $description = $filePath = "";
$fileError = "";
$categoryId = 0;
$message = $_SESSION["portfolio_message"] ?? "";
unset($_SESSION["portfolio_message"]);

if ($isEdit)
{
    // A student can edit only their own submission.
    $stmt = $conn->prepare("SELECT title, category_id, tech_stack, description, file_path FROM projects WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $editId, $userId);
    $stmt->execute();
    $project = $stmt->get_result()->fetch_assoc();

    if (!$project)
    {
        exit();
    }

    $title = $project["title"];
    $categoryId = $project["category_id"];
    $techStack = $project["tech_stack"];
    $description = $project["description"];
    $filePath = $project["file_path"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $title = trim($_POST["title"] ?? "");
    $categoryId = ($_POST["category_id"] ?? 0);
    $techStack = trim($_POST["tech_stack"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $upload = $_FILES["document"] ?? null;
    $hasFile = $upload && $upload["error"] != UPLOAD_ERR_NO_FILE;

    // PHP rejects invalid form data without showing another error message.
    $valid = $title != "" && $categoryId > 0 && $techStack != "" && $description != ""
             && strlen($title) <= 200 && strlen($techStack) <= 255 && ($isEdit || $hasFile);

    if ($valid)
    {
        $stmt = $conn->prepare("SELECT id FROM categories WHERE id = ?");
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $valid = $stmt->get_result()->fetch_assoc();
    }

    $oldFilePath = $filePath;
    $newFile = "";

    if ($valid && $hasFile)
    {
        // Check file size and allowed file types before saving.
        if ($upload["error"] == UPLOAD_ERR_INI_SIZE || $upload["error"] == UPLOAD_ERR_FORM_SIZE ||
            $upload["size"] > 5 * 1024 * 1024)
        {
            $fileError = "File size must not exceed 5 MB.";
        }
        elseif ($upload["error"] != UPLOAD_ERR_OK || $upload["size"] == 0)
        {
            $fileError = "File upload failed. Please try again.";
        }
        else
        {
            $extension = strtolower(pathinfo($upload["name"], PATHINFO_EXTENSION));
            $mimeType = mime_content_type($upload["tmp_name"]);
            $correctType = false;

            if ($extension == "pdf" && $mimeType == "application/pdf")
            {
                $correctType = true;
            }
            elseif ($extension == "txt" && $mimeType == "text/plain")
            {
                $correctType = true;
            }
            elseif ($extension == "docx" || $extension == "zip")
            {
                // DOCX and ZIP both use a ZIP file structure.
                $fileHeader = file_get_contents($upload["tmp_name"], false, null, 0, 4);
                $correctType = $fileHeader == "PK\x03\x04" || $fileHeader == "PK\x05\x06";
            }

            if (!$correctType)
            {
                $fileError = "Only PDF, DOCX, TXT or ZIP files are allowed.";
            }
            else
            {
                $fileName = uniqid("project_") . "." . $extension;
                $newFile = __DIR__ . "/../uploads/" . $fileName;
                $filePath = "uploads/" . $fileName;
                $valid = move_uploaded_file($upload["tmp_name"], $newFile);
                if (!$valid) $fileError = "File upload failed. Please try again.";
            }
        }

        if ($fileError != "") $valid = false;
    }

    if ($valid)
    {
        if ($isEdit)
        {
            $stmt = $conn->prepare("UPDATE projects SET title = ?, category_id = ?, tech_stack = ?, description = ?, file_path = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sisssii", $title, $categoryId, $techStack, $description, $filePath, $editId, $userId);
        }
        else
        {
            $stmt = $conn->prepare("INSERT INTO projects (user_id, category_id, title, description, tech_stack, file_path) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissss", $userId, $categoryId, $title, $description, $techStack, $filePath);
        }

        if ($stmt->execute())
        {
            if ($isEdit && $newFile != "" && $oldFilePath != "")
            {
                $oldFile = __DIR__ . "/../uploads/" . basename($oldFilePath);
                if (is_file($oldFile)) unlink($oldFile);
            }

            $_SESSION["portfolio_message"] = "Project saved successfully!";
            header("Location: submit_project.php" . ($isEdit ? "?edit=" . $editId : ""));
            exit();
        }
        elseif ($newFile != "" && is_file($newFile))
        {
            unlink($newFile);
        }
    }
}

$stmt = $conn->prepare("SELECT id, category_name FROM categories ORDER BY category_name");
$stmt->execute();
$categories = $stmt->get_result();

require_once "../includes/header.php";
?>
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="text-primary mb-4">
                    <i class="bi bi-<?php echo $isEdit ? "pencil-square" : "cloud-arrow-up-fill"; ?>"></i>
                    <?php echo $isEdit ? "Edit Portfolio Project" : "Submit Portfolio Project"; ?>
                </h3>

                <?php if ($message != ""): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <?php if ($fileError != ""): ?>
                    <div class="alert alert-danger"><?php echo $fileError; ?></div>
                <?php endif; ?>

                <form method="POST" action="submit_project.php<?php echo $isEdit ? "?edit=" . $editId : ""; ?>" enctype="multipart/form-data" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Project Title</label>
                        <input type="text" name="title" class="form-control" maxlength="200" value="<?php echo htmlspecialchars($title); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- Select Category --</option>
                            <?php while ($row = $categories->fetch_assoc()): ?>
                                <option value="<?php echo $row["id"]; ?>" <?php echo $row["id"] == $categoryId ? "selected" : ""; ?>><?php echo htmlspecialchars($row["category_name"]); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tech Stack (Comma Separated)</label>
                        <input type="text" name="tech_stack" class="form-control" maxlength="255" placeholder="e.g. PHP, MySQL, Bootstrap 5, AJAX" value="<?php echo htmlspecialchars($techStack); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Project Description</label>
                        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Documentation (PDF, DOCX, TXT or ZIP - Max 5 MB)</label>
                        <input type="file" name="document" class="form-control" accept=".pdf,.docx,.txt,.zip" <?php echo $isEdit ? 'data-optional="true"' : ""; ?>>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $isEdit ? "Save Changes" : "Submit Project"; ?></button>
                    <?php if ($isEdit): ?>
                        <a href="my_projects.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../validation.js"></script>

<?php include "../includes/footer.php"; ?>