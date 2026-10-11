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