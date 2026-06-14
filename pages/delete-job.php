<?php require_once "../includes/header.php"; ?>
<?php require_once "../config/database.php"; ?>
<?php require_once "../includes/auth.php"; ?>

<?php
// Step 1: Get job id from URL
$job_id = $_GET["id"];

$pdo = connectDB();

// Step 2: Get this job from database
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = :id");
$stmt->execute([":id" => $job_id]);
$job = $stmt->fetch();

// Step 3: Check if job exists
if (!$job) {
    echo "Job not found!";
    exit();
}

// Step 4: Make sure this job belongs to logged in user
if ($job["user_id"] != $_SESSION["user_id"]) {
    echo "You are not allowed to delete this job!";
    exit();
}

// Step 5: Delete the job
$stmt = $pdo->prepare("DELETE FROM jobs WHERE id = :id");
$stmt->execute([":id" => $job_id]);

// Step 6: Send back to dashboard
header("Location: dashboard.php");
exit();
?>