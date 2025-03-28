<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define upload directory
$uploadDir = "uploads/";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Get all files
$files = array_diff(scandir($uploadDir), array('.', '..'));

// Define storage limit (500MB)
$maxStorage = 500 * 1024 * 1024;
$usedStorage = 0;
$cleanupSuggestions = [];
$fileStats = [
    "Images" => ["size" => 0, "count" => 0],
    "Documents" => ["size" => 0, "count" => 0],
    "Archive" => ["size" => 0, "count" => 0],
    "Media" => ["size" => 0, "count" => 0],
    "Others" => ["size" => 0, "count" => 0]
];

$fileTypes = [
    "Images" => ["jpg", "jpeg", "png", "gif", "webp"],
    "Documents" => ["pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "txt"],
    "Archive" => ["zip", "rar", "tar", "7z"],
    "Media" => ["mp3", "mp4", "avi", "mkv", "wav"]
];

// Categorize files and analyze storage
foreach ($files as $file) {
    $filePath = $uploadDir . $file;
    $fileSize = filesize($filePath);
    $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $lastModified = filemtime($filePath);

    $usedStorage += $fileSize;
    $category = "Others";

    foreach ($fileTypes as $cat => $extensions) {
        if (in_array($fileExt, $extensions)) {
            $category = $cat;
            break;
        }
    }

    $fileStats[$category]["size"] += $fileSize;
    $fileStats[$category]["count"]++;

    // AI-Based Cleanup Analysis
    if ($fileSize > 50 * 1024 * 1024) { // Files larger than 50MB
        $cleanupSuggestions[] = ["file" => $file, "reason" => "Large file (>50MB)"];
    }
    if (time() - $lastModified > 6 * 30 * 24 * 60 * 60) { // Not accessed in 6 months
        $cleanupSuggestions[] = ["file" => $file, "reason" => "Not accessed in over 6 months"];
    }
}

// Used storage percentage
$usedPercentage = ($usedStorage / $maxStorage) * 100;

// Trigger warning if storage is over 80%
$storageAlert = ($usedPercentage > 80) ? "⚠️ Storage is almost full! Consider cleaning up unnecessary files." : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI File Organizer</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="theme-toggle">
    <button id="darkModeToggle">
        <i class="fas fa-moon"></i>
    </button>
</div>

<div class="container">
    <h1>Dashboard</h1>

    <?php if ($storageAlert): ?>
        <p class="alert"><?= $storageAlert ?></p>
    <?php endif; ?>

    <!-- File Grid -->
    <div class="file-grid">
        <?php if (!empty($files)): ?>
            <?php foreach ($files as $file): ?>
                <div onclick="window.open('uploads/<?= $file ?>', '_blank')" title="<?= $file ?>" class="file-box">
                    <i class="fas fa-file"></i>
                    <p><?= $file ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No files uploaded yet.</p>
        <?php endif; ?>
    </div>

    <a href="index.php" class="view-files">
        <i class="fas fa-upload"></i> Upload More Files
    </a>

    <!-- Storage Info -->
    <div class="storage-box">
        <h2>Storage Usage</h2>
        <div class="progress-circle">
            <svg width="120" height="120">
                <circle cx="60" cy="60" r="50" stroke="#ddd" stroke-width="8" fill="none"/>
                <circle cx="60" cy="60" r="50" stroke="#007bff" stroke-width="8" fill="none"
                        stroke-dasharray="314.16" stroke-dashoffset="<?= 314.16 - (314.16 * ($usedPercentage / 100)) ?>" />
            </svg>
            <div class="progress-text">
                <p><?= round($usedStorage / (1024 * 1024), 2); ?> MB</p>
                <span>Used of 500 MB</span>
            </div>
        </div>

        <h2>File Categories</h2>
        <div class="file-categories">
            <?php foreach ($fileStats as $category => $data): ?>
                <div class="category">
                    <i class="fas fa-folder"></i>
                    <div>
                        <span><?= $category ?></span>
                        <span><?= round($data["size"] / (1024 * 1024), 2) ?> MB</span>
                        <span><?= $data["count"] ?> Files</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- AI Cleanup Suggestions -->
        <h2>AI Cleanup Suggestions</h2>
        <div class="cleanup-suggestions">
            <?php if (!empty($cleanupSuggestions)): ?>
                <ul>
                    <?php foreach ($cleanupSuggestions as $suggestion): ?>
                        <li>
                            <strong><?= $suggestion["file"] ?></strong>: <?= $suggestion["reason"] ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No cleanup suggestions at this time.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="dark-mode.js"></script> <!-- External JS for dark mode -->

</body>
</html>
