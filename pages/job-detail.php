<?php require_once "../includes/header.php"; ?>
<?php require_once "../config/database.php"; ?>

<?php
$pdo = connectDB();
$job_id = $_GET["id"];

$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = :id");
$stmt->execute([":id" => $job_id]);
$job = $stmt->fetch();

if (!$job) {
    echo "<div class='page-wrap'><div class='container text-center'><h2>Job not found</h2><a href='jobs.php' class='btn btn-primary mt-16'>Back to Jobs</a></div></div>";
    require_once "../includes/footer.php";
    exit();
}

$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
$stmt->execute([":id" => $job["user_id"]]);
$poster = $stmt->fetch();
?>

<!-- Detail Header -->
<div class="detail-header">
    <div class="container">

        <a href="jobs.php" class="btn btn-sm mb-20" style="color:rgba(255,255,255,0.6); background:rgba(255,255,255,0.08); border-radius:var(--radius-sm);">
            <i class="bi bi-arrow-left"></i> Back to Jobs
        </a>

        <div class="d-flex align-center gap-16 flex-wrap">
            <div class="user-avatar" style="width:64px;height:64px;font-size:1.5rem;border-radius:14px;flex-shrink:0;">
                <?php echo strtoupper(substr($job["company"], 0, 1)); ?>
            </div>
            <div>
                <h1><?php echo htmlspecialchars($job["title"]); ?></h1>
                <div class="detail-header-meta">
                    <span><i class="bi bi-building"></i> <?php echo htmlspecialchars($job["company"]); ?></span>
                    <span><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($job["location"]); ?></span>
                    <span><i class="bi bi-calendar3"></i> <?php echo date("M d, Y", strtotime($job["created_at"])); ?></span>
                </div>
                <div class="d-flex flex-wrap gap-8">
                    <span class="badge badge-primary"><i class="bi bi-currency-dollar"></i> <?php echo htmlspecialchars($job["salary"]); ?></span>
                    <span class="badge badge-muted"><i class="bi bi-clock"></i> <?php echo htmlspecialchars($job["type"]); ?></span>
                    <?php if ($job["location"] == "Remote") { ?>
                        <span class="badge badge-accent"><i class="bi bi-wifi"></i> Remote</span>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Content -->
<div class="page-wrap">
    <div class="container">
        <div class="row g-4">

            <!-- Left: Job info -->
            <div class="col-12 col-lg-8">

                <!-- Info tiles -->
                <div class="card mb-4" data-aos="fade-up">
                    <div class="card-body">
                        <h3 style="font-size:1rem; margin-bottom:16px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em; font-size:0.8rem;">Job Overview</h3>
                        <div class="info-grid">
                            <div class="info-tile">
                                <div class="info-tile-label">Job Title</div>
                                <div class="info-tile-value"><?php echo htmlspecialchars($job["title"]); ?></div>
                            </div>
                            <div class="info-tile">
                                <div class="info-tile-label">Company</div>
                                <div class="info-tile-value"><?php echo htmlspecialchars($job["company"]); ?></div>
                            </div>
                            <div class="info-tile">
                                <div class="info-tile-label">Location</div>
                                <div class="info-tile-value"><?php echo htmlspecialchars($job["location"]); ?></div>
                            </div>
                            <div class="info-tile">
                                <div class="info-tile-label">Job Type</div>
                                <div class="info-tile-value"><?php echo htmlspecialchars($job["type"]); ?></div>
                            </div>
                            <div class="info-tile">
                                <div class="info-tile-label">Salary</div>
                                <div class="info-tile-value"><?php echo htmlspecialchars($job["salary"]); ?></div>
                            </div>
                            <div class="info-tile">
                                <div class="info-tile-label">Posted On</div>
                                <div class="info-tile-value"><?php echo date("M d, Y", strtotime($job["created_at"])); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body">
                        <h3 style="font-size:1.1rem; margin-bottom:16px; font-family:var(--font-display);">Job Description</h3>
                        <p style="color:var(--text-muted); line-height:1.8;">
                            <?php echo nl2br(htmlspecialchars($job["description"])); ?>
                        </p>
                    </div>
                </div>

            </div>

            <!-- Right: Apply card -->
            <div class="col-12 col-lg-4">
                <div class="apply-card" data-aos="fade-left">
                    <h3>Interested in this role?</h3>
                    <p>Contact the employer directly to apply for this position.</p>

                    <?php if (isset($_SESSION["user_id"])) { ?>

                        <?php
                        // Check if already applied
                        $chk = $pdo->prepare("SELECT id FROM applications WHERE job_id = :jid AND user_id = :uid");
                        $chk->execute([":jid" => $job["id"], ":uid" => $_SESSION["user_id"]]);
                        $applied = $chk->fetch();
                        ?>

                        <?php if ($applied) { ?>
                            <div class="btn btn-block btn-lg" style="background:rgba(6,214,160,0.15); color:var(--accent); cursor:default; justify-content:center;">
                                <i class="bi bi-check-circle-fill"></i> Already Applied
                            </div>
                            <a href="my-applications.php" class="btn btn-outline btn-block mt-8" style="border-color:rgba(255,255,255,0.2); color:rgba(255,255,255,0.6);">
                                View My Applications
                            </a>
                        <?php } else { ?>
                            <a href="apply.php?job_id=<?php echo $job["id"]; ?>" class="btn btn-accent btn-block btn-lg">
                                <i class="bi bi-send-fill"></i> Apply Now
                            </a>
                        <?php } ?>

                        <p style="color:rgba(255,255,255,0.4); font-size:0.8rem; margin-top:14px;">
                            Posted by <?php echo htmlspecialchars($poster["name"]); ?>
                        </p>

                    <?php } else { ?>
                        <a href="login.php" class="btn btn-primary btn-block btn-lg">
                            <i class="bi bi-box-arrow-in-right"></i> Login to Apply
                        </a>
                        <p style="color:rgba(255,255,255,0.4); font-size:0.8rem; margin-top:14px;">
                            You need an account to apply
                        </p>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
