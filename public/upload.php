<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set upload directory
$uploadDir = "uploads/";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Allowed file types
$allowedTypes = [
    "jpg", "jpeg", "png", "gif", "webp",  // Images
    "pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "txt",  // Documents
    "zip", "rar", "tar", "7z",  // Archives
    "mp3", "mp4", "avi", "mkv", "wav"  // Media
];

// Max file size (200MB)
$maxFileSize = 200 * 1024 * 1024;
$logFile = "upload_error.log"; // Log file for errors

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if file is uploaded correctly
    if (!isset($_FILES["file"]) || $_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
        file_put_contents($logFile, "Upload Error Code: " . $_FILES["file"]["error"] . "\n", FILE_APPEND);
        header("Location: index.php?upload=error&msg=File upload error (Error Code: " . $_FILES["file"]["error"] . ")");
        exit();
    }

    $fileName = basename($_FILES["file"]["name"]);
    $fileSize = $_FILES["file"]["size"];
    $fileTmpPath = $_FILES["file"]["tmp_name"];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Check file type
    if (!in_array($fileExt, $allowedTypes)) {
        file_put_contents($logFile, "Unsupported file type: $fileName ($fileExt)\n", FILE_APPEND);
        header("Location: index.php?upload=error&msg=Unsupported file type.");
        exit();
    }

    // Check file size
    if ($fileSize > $maxFileSize) {
        file_put_contents($logFile, "File size exceeds limit: $fileName ($fileSize bytes)\n", FILE_APPEND);
        header("Location: index.php?upload=error&msg=File size exceeds 200MB.");
        exit();
    }

    // Prevent overwriting files
    $uniqueFileName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "_", $fileName);
    $filePath = $uploadDir . $uniqueFileName;

    // Move uploaded file
    if (move_uploaded_file($fileTmpPath, $filePath)) {
        header("Location: index.php?upload=success");
    } else {
        file_put_contents($logFile, "Failed to move uploaded file: $fileName\n", FILE_APPEND);
        header("Location: index.php?upload=error&msg=File upload failed.");
    }
}
?>
