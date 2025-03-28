<?php
// Check for upload messages
$uploadMessage = "";
if (isset($_GET["upload"])) {
    if ($_GET["upload"] === "success") {
        $uploadMessage = "<p class='success-msg'>🎉 File uploaded successfully!</p>";
    } elseif ($_GET["upload"] === "error" && isset($_GET["msg"])) {
        $uploadMessage = "<p class='error-msg'>⚠️ " . htmlspecialchars($_GET["msg"]) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Directory Manager</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="theme-toggle">
    <button id="darkModeToggle">
        <i id="themeIcon" class="fas fa-moon"></i>
    </button>
</div>

<div class="container">
    <h1>Upload a File</h1>

    <?= $uploadMessage ?> <!-- Display Upload Message -->

    <form action="upload.php" method="POST" enctype="multipart/form-data" class="upload-box">
        <label for="fileUpload" class="file-label">
            <i class="fas fa-upload"></i> Choose a file or drag it here
        </label>
        <input type="file" id="fileUpload" name="file" required>
        <button type="submit" class="upload-btn">Upload</button>
    </form>

    <a href="dashboard.php" class="view-files">
        <i class="fas fa-folder"></i> View Uploaded Files
    </a>
</div>

<script>
// Fade out messages after 3 seconds
setTimeout(() => {
    const msg = document.querySelector('.success-msg, .error-msg');
    if (msg) msg.style.opacity = '0';
}, 3000);

// Remove query parameters after 3 seconds
setTimeout(() => {
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.pathname);
    }
}, 3000);

// Dark Mode Toggle
const darkModeToggle = document.getElementById("darkModeToggle");
const themeIcon = document.getElementById("themeIcon");
const body = document.body;

// Load dark mode preference
if (localStorage.getItem("darkMode") === "enabled") {
    body.classList.add("dark-mode");
    themeIcon.classList.replace("fa-moon", "fa-sun");
}

// Toggle dark mode
darkModeToggle.addEventListener("click", () => {
    body.classList.toggle("dark-mode");
    const isDarkMode = body.classList.contains("dark-mode");
    localStorage.setItem("darkMode", isDarkMode ? "enabled" : "disabled");
    themeIcon.classList.replace(isDarkMode ? "fa-moon" : "fa-sun", isDarkMode ? "fa-sun" : "fa-moon");
});
</script>

</body>
</html>
