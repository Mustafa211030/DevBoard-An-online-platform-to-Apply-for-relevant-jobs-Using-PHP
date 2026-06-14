<?php require_once "../includes/header.php"; ?>
<?php require_once "../config/database.php"; ?>
<?php require_once "../includes/auth.php"; ?>

<?php is_logged_in(); ?>

<?php
$pdo = connectDB();

$stmt = $pdo->prepare("
    SELECT applications.*, jobs.title as job_title, jobs.company, jobs.location, jobs.type, jobs.salary
    FROM applications
    LEFT JOIN jobs ON applications.job_id = jobs.id
    WHERE applications.user_id = :user_id
    ORDER BY applications.applied_at DESC
");
$stmt->execute([":user_id" => $_SESSION["user_id"]]);
$my_applications = $stmt->fetchAll();
?>

<div class="dashboard-header">
    <div class="container">
        <h1 class="dashboard-title"><i class="bi bi-file-earmark-text"></i> My <span>Applications</span></h1>
        <p class="dashboard-subtitle">Track all the jobs you have applied for</p>
    </div>
</div>

<div class="page-wrap">
    <div class="container">

        <div class="section-header" data-aos="fade-up">
            <h2>All Applications <span style="color:var(--primary);">(<?php echo count($my_applications); ?>)</span></h2>
            <a href="jobs.php" class="btn btn-outline btn-sm">
                <i class="bi bi-search"></i> Find More Jobs
            </a>
        </div>

        <?php if (count($my_applications) == 0) { ?>

            <div class="card card-body text-center" style="padding:56px;" data-aos="zoom-in">
                <i class="bi bi-file-earmark-x" style="font-size:2.5rem; color:var(--text-light);"></i>
                <h3 class="mt-16" style="font-size:1.2rem;">No applications yet</h3>
                <p class="text-muted mt-8">Start applying for jobs to track them here.</p>
                <a href="jobs.php" class="btn btn-primary mt-16" style="width:fit-content; margin:16px auto 0;">
                    <i class="bi bi-briefcase"></i> Browse Jobs
                </a>
            </div>

        <?php } else { ?>

            <?php foreach ($my_applications as $i => $app) { ?>

                <div class="job-card" data-aos="fade-up" data-aos-delay="<?php echo $i * 60; ?>">

                    <div class="d-flex align-center gap-12 flex-wrap">

                        <!-- Company avatar -->
                        <div class="user-avatar" style="width:48px;height:48px;font-size:1.1rem;border-radius:10px;flex-shrink:0;">
                            <?php echo strtoupper(substr($app["company"], 0, 1)); ?>
                        </div>

                        <!-- Job info -->
                        <div style="flex:1; min-width:0;">
                            <div class="job-card-title"><?php echo htmlspecialchars($app["job_title"]); ?></div>
                            <div class="job-card-meta" style="margin-bottom:0;">
                                <span><i class="bi bi-building"></i><?php echo htmlspecialchars($app["company"]); ?></span>
                                <span><i class="bi bi-geo-alt"></i><?php echo htmlspecialchars($app["location"]); ?></span>
                                <span><i class="bi bi-currency-dollar"></i><?php echo htmlspecialchars($app["salary"]); ?></span>
                            </div>
                        </div>

                        <!-- Status badge -->
                        <div style="flex-shrink:0;">
                            <?php
                            $statusMap = [
                                "pending"     => ["badge-warning",  "bi-hourglass-split",    "Pending"],
                                "reviewed"    => ["badge-primary",  "bi-eye-fill",           "Reviewed"],
                                "shortlisted" => ["badge-accent",   "bi-star-fill",          "Shortlisted"],
                                "rejected"    => ["badge-danger",   "bi-x-circle-fill",      "Rejected"],
                            ];
                            $s = $statusMap[$app["status"]] ?? $statusMap["pending"];
                            ?>
                            <span class="badge <?php echo $s[0]; ?>">
                                <i class="bi <?php echo $s[1]; ?>"></i>
                                <?php echo $s[2]; ?>
                            </span>
                        </div>

                    </div>

                    <!-- Footer row -->
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-top:14px; padding-top:12px; border-top:1px solid var(--border-soft); flex-wrap:wrap; gap:8px;">
                        <div style="font-size:0.78rem; color:var(--text-light);">
                            <i class="bi bi-calendar3"></i> Applied on <?php echo date("M d, Y", strtotime($app["applied_at"])); ?>
                            &nbsp;&bull;&nbsp;
                            <i class="bi bi-file-earmark-pdf"></i> CV: <?php echo htmlspecialchars($app["cv_filename"]); ?>
                        </div>
                        <a href="job-detail.php?id=<?php echo $app["job_id"]; ?>" class="btn btn-outline btn-sm">
                            View Job <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                </div>

            <?php } ?>

        <?php } ?>

    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
