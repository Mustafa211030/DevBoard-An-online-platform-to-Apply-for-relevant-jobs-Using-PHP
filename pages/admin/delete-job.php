<?php
require_once "../../includes/header.php";
require_once "../../config/database.php";
require_once "../../includes/auth.php";
?>

<?php is_admin(); ?>

<?php
$job_id = $_GET["id"];

$pdo = connectDB();

$stmt = $pdo->prepare("DELETE FROM jobs WHERE id = :id");
$stmt->execute([":id" => $job_id]);

header("Location: index.php");
exit();
?>