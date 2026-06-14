<?php
require_once "../includes/header.php";
require_once "../config/database.php";
require_once "../includes/auth.php";
?>

<?php is_logged_in(); ?>

<?php
$title = $company = $location = $salary = $type = $description = "";
$error_message = $success_message = "";

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
        $pdo = connectDB();
        $stmt = $pdo->prepare("INSERT INTO jobs (title, company, location, salary, type, description, user_id) VALUES (:title, :company, :location, :salary, :type, :description, :user_id)");
        $stmt->execute([
            ":title"       => $title,
            ":company"     => $company,
            ":location"    => $location,
            ":salary"      => $salary,
            ":type"        => $type,
            ":description" => $description,
            ":user_id"     => $_SESSION["user_id"]
        ]);
        $success_message = "Job posted successfully!";
        $title = $company = $location = $salary = $type = $description = "";
    }
}
?>

<!-- Page Header -->
<div class="dashboard-header">
    <div class="container">
        <h1 class="dashboard-title"><i class="bi bi-plus-circle"></i> Post a <span>New Job</span></h1>
        <p class="dashboard-subtitle">Fill in the details below to publish your job listing</p>
    </div>
</div>

<div class="page-wrap">
    <div class="container">
        <div class="form-card" style="max-width:680px;" data-aos="fade-up">

            <?php if ($error_message != "") { ?>
                <div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> <?php echo $error_message; ?></div>
            <?php } ?>

            <?php if ($success_message != "") { ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <?php echo $success_message; ?>
                    <a href="jobs.php" style="font-weight:600; margin-left:6px;">View all jobs</a>
                </div>
            <?php } ?>

            <form method="POST" action="">

                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label><i class="bi bi-briefcase"></i> Job Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" placeholder="e.g. Senior PHP Developer">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="bi bi-building"></i> Company Name</label>
                            <input type="text" name="company" class="form-control" value="<?php echo htmlspecialchars($company); ?>" placeholder="e.g. TechCorp">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="bi bi-geo-alt"></i> Location</label>
                            <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($location); ?>" placeholder="e.g. Remote or New York">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="bi bi-cash-stack"></i> Salary</label>
                            <input type="text" name="salary" class="form-control" value="<?php echo htmlspecialchars($salary); ?>" placeholder="e.g. $3000/mo">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="bi bi-tag"></i> Job Type</label>
                            <select name="type" class="form-control">
                                <option value="">-- Select Type --</option>
                                <?php foreach (["Full-time","Part-time","Contract","Freelance"] as $t) { ?>
                                    <option value="<?php echo $t; ?>" <?php if ($t == $type) echo "selected"; ?>><?php echo $t; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label><i class="bi bi-text-paragraph"></i> Job Description</label>
                            <textarea name="description" class="form-control" placeholder="Describe the role, requirements, responsibilities..."><?php echo htmlspecialchars($description); ?></textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block mt-8">
                    <i class="bi bi-send-fill"></i> Post Job
                </button>

            </form>

        </div>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
