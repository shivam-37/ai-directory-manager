AI Directory Manager 🚀
An AI-powered directory management system that helps users manage file storage by providing smart storage suggestions, cleanup recommendations, and alerts when storage is nearly full.

🌟 Features
✔ File Upload System – Upload and manage files up to 200MB.
✔ AI-Powered Storage Management – AI analyzes file content to suggest storage categories.
✔ Smart Cleanup Recommendations – Detects large, old, and unnecessary files for deletion.
✔ AI-Powered Search – Find files based on content, type, or metadata.
✔ Storage Alerts – Alerts when storage reaches critical levels.
✔ Supports Multiple File Types – Images, documents, archives, media files, and more.
✔ Dark Mode UI – User-friendly interface with light/dark mode toggle.

📁 Supported File Types
Documents: PDF, DOCX, TXT, PPTX, XLSX

Images: JPG, PNG, WEBP, GIF

Archives: ZIP, RAR, 7Z, TAR

Media Files: MP3, MP4, AVI, MKV, WAV

🚀 Installation & Setup
1️⃣ Clone the Repository
sh
Copy
Edit
git clone https://github.com/shivam-37/ai-directory-manager.git
cd ai-directory-manager
2️⃣ Install Dependencies
Ensure you have PHP 8+ installed.

sh
Copy
Edit
composer install  # If using Composer
npm install       # If using Node for UI enhancements
3️⃣ Configure PHP Settings
Modify your php.ini file to support large file uploads:

ini
Copy
Edit
upload_max_filesize = 200M
post_max_size = 210M
memory_limit = 256M
4️⃣ Run the Project
sh
Copy
Edit
php -S localhost:8000
Visit: http://localhost:8000 in your browser.
