<?php require_once "../includes/header.php"; ?>
<?php require_once "../config/database.php"; ?>
<?php require_once "../includes/auth.php"; ?>

<?php is_logged_in(); ?>

<?php
$error_message = "";
$success_message = "";
$job_id = $_GET["id"];
$pdo = connectDB();

$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = :id");
$stmt->execute([":id" => $job_id]);
$job = $stmt->fetch();

if (!$job) { echo "<div class='page-wrap container'><p>Job not found!</p></div>"; exit(); }
if ($job["user_id"] != $_SESSION["user_id"]) { echo "<div class='page-wrap container'><p>Not allowed!</p></div>"; exit(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title       = $_POST["title"];
    $company     = $_POST["company"];
    $location    = $_POST["location"];
    $salary      = $_POST["salary"];
    $type        = $_POST["type"];
    $description = $_POST["description"];

    if ($title == "")       $error_message = "Job title is required";
    elseif ($company == "") $error_message = "Company name is required";
    elseif ($location == "") $error_message = "Location is required";
    elseif ($salary == "")  $error_message = "Salary is required";
    elseif ($type == "")    $error_message = "Job type is required";
    elseif ($description == "") $error_message = "Description is required";
    else {
        $stmt = $pdo->prepare("UPDATE jobs SET title=:title, company=:company, location=:location, salary=:salary, type=:type, description=:description WHERE id=:id");
        $stmt->execute([":title"=>$title,":company"=>$company,":location"=>$location,":salary"=>$salary,":type"=>$type,":description"=>$description,":id"=>$job_id]);
        $success_message = "Job updated successfully!";
        $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = :id");
        $stmt->execute([":id" => $job_id]);
        $job = $stmt->fetch();
    }
}
?>

<div class="dashboard-header">
    <div class="container">
        <h1 class="dashboard-title"><i class="bi bi-pencil-square"></i> Edit <span>Job</span></h1>
        <p class="dashboard-subtitle"><?php echo htmlspecialchars($job["title"]); ?> at <?php echo htmlspecialchars($job["company"]); ?></p>
    </div>
</div>

<div class="page-wrap">
    <div class="container">

        <a href="dashboard.php" class="btn btn-outline btn-sm mb-24">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>

        <div class="form-card" style="max-width:680px;" data-aos="fade-up">

            <?php if ($error_message != "") { ?>
                <div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> <?php echo $error_message; ?></div>
            <?php } ?>
            <?php if ($success_message != "") { ?>
                <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> <?php echo $success_message; ?></div>
            <?php } ?>

            <form method="POST" action="">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label><i class="bi bi-briefcase"></i> Job Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($job["title"]); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="bi bi-building"></i> Company</label>
                            <input type="text" name="company" class="form-control" value="<?php echo htmlspecialchars($job["company"]); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="bi bi-geo-alt"></i> Location</label>
                            <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($job["location"]); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="bi bi-cash-stack"></i> Salary</label>
                            <input type="text" name="salary" class="form-control" value="<?php echo htmlspecialchars($job["salary"]); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="bi bi-tag"></i> Job Type</label>
                            <select name="type" class="form-control">
                                <option value="">-- Select --</option>
                                <?php foreach (["Full-time","Part-time","Contract","Freelance"] as $t) { ?>
                                    <option value="<?php echo $t; ?>" <?php if ($t == $job["type"]) echo "selected"; ?>><?php echo $t; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label><i class="bi bi-text-paragraph"></i> Description</label>
                            <textarea name="description" class="form-control"><?php echo htmlspecialchars($job["description"]); ?></textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-8">
                    <i class="bi bi-save-fill"></i> Update Job
                </button>
            </form>

        </div>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
