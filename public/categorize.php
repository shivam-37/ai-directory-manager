<?php
require_once __DIR__ . '/src/Categorizer.php';

use Project\AiDirectory\Categorizer;

$category = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filePath = $uploadDir . basename($_FILES['file']['name']);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
        $categorizer = new Categorizer();
        $category = $categorizer->categorize($filePath);
    } else {
        $category = "Error uploading file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Categorization Result</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to CSS -->
</head>
<body>
    <div class="container">
        <h2>File uploaded successfully!</h2>
        <p><strong>Predicted Category:</strong> <?= htmlspecialchars($category) ?></p>
        <a href="index.php" class="btn">Upload Another File</a>
    </div>
</body>
</html>
