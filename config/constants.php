<?php
define("SITE_NAME",    "DevBoard");
define("SITE_TAGLINE", "Find Your Next Developer Job");
define("SITE_YEAR",    date("Y"));

$protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host      = $_SERVER['HTTP_HOST'];
$subfolder = "";

define("BASE_URL",    $protocol . "://" . $host . $subfolder);
define("UPLOADS_DIR", __DIR__ . "/../uploads/cvs/");
define("UPLOADS_URL", BASE_URL . "/uploads/cvs/");

define("ALLOWED_EXTENSIONS", ["pdf", "doc", "docx"]);
define("MAX_FILE_SIZE", 5 * 1024 * 1024);
?>