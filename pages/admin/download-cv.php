<?php
// ============================================================
// Secure CV Download — Admin Only
// This file serves CV files securely.
// It NEVER exposes the real file path to the user.
// Only admins can download CVs.
// ============================================================

session_start();
require_once "../../config/constants.php";
require_once "../../config/database.php";

// 1. Check admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["is_admin"] != 1) {
    http_response_code(403);
    die("Access denied.");
}

// 2. Get application ID from URL
$app_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($app_id == 0) {
    http_response_code(400);
    die("Invalid request.");
}

// 3. Get application from database
$pdo  = connectDB();
$stmt = $pdo->prepare("SELECT * FROM applications WHERE id = :id");
$stmt->execute([":id" => $app_id]);
$app  = $stmt->fetch();

if (!$app) {
    http_response_code(404);
    die("Application not found.");
}

// 4. Build the real file path
$file_path = UPLOADS_DIR . $app["cv_filename"];

// 5. Make sure the file actually exists on disk
if (!file_exists($file_path)) {
    http_response_code(404);
    die("CV file not found on server.");
}

// 6. Get the file extension to set correct Content-Type
$ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

$mime_types = [
    "pdf"  => "application/pdf",
    "doc"  => "application/msword",
    "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
];

$mime = $mime_types[$ext] ?? "application/octet-stream";

// 7. Create a clean download filename for the admin
// Format: CV_ApplicantName_JobID.ext
$clean_name = "CV_" . preg_replace("/[^a-zA-Z0-9]/", "_", $app["full_name"]) . "_Job" . $app["job_id"] . "." . $ext;

// 8. Send headers and stream the file
header("Content-Type: " . $mime);
header("Content-Disposition: attachment; filename=\"" . $clean_name . "\"");
header("Content-Length: " . filesize($file_path));
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Stream file to browser
readfile($file_path);
exit();
?>
