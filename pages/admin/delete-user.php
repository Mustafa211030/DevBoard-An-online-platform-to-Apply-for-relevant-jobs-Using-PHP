<?php
require_once "../../includes/header.php";
require_once "../../config/database.php";
require_once "../../includes/auth.php";
?>

<?php is_admin(); ?>

<?php
$user_id = $_GET["id"];

$pdo = connectDB();

// Make sure you cannot delete an admin
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([":id" => $user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found!";
    exit();
}

if ($user["is_admin"] == 1) {
    echo "Cannot delete an admin!";
    exit();
}

// Delete all jobs by this user first
$stmt = $pdo->prepare("DELETE FROM jobs WHERE user_id = :user_id");
$stmt->execute([":user_id" => $user_id]);

// Then delete the user
$stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
$stmt->execute([":id" => $user_id]);

// Go back to users list
header("Location: users.php");
exit();
?>