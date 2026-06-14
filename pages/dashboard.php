<?php require_once "../includes/header.php"; ?>
<?php require_once "../config/database.php"; ?>
<?php require_once "../includes/auth.php"; ?>

<?php is_logged_in(); ?>

<?php
$user_id   = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];
$pdo = connectDB();

$stmt = $pdo->prepare("SELECT * FROM jobs WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute([":user_id" => $user_id]);
$my_jobs = $stmt->fetchAll();
$total = count($my_jobs);
?>

<!-- Dashboard Header -->
<div class="dashboard-header">
    <div class="container">
        <div class="d-flex align-center gap-16 flex-wrap">
            <div class="user-avatar" style="width:56px;height:56px;font-size:1.4rem;flex-shrink:0;">
                <?php echo strtoupper(substr($user_name, 0, 1)); ?>
            </div>
            <div>
                <h1 class="dashboard-title">Hello, <span><?php echo htmlspecialchars(explode(" ", $user_name)[0]); ?></span> 👋</h1>
                <p class="dashboard-subtitle">Manage your job listings from here</p>
            </div>
        </div>
    </div>
</div>

<div class="page-wrap">
    <div class="container">

        <!-- Stats -->
        <div class="stats-grid" data-aos="fade-up">
            <div class="stat-card">
                <div class="stat-card-number"><?php echo $total; ?></div>
                <div class="stat-card-label">Jobs Posted</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-number"><?php echo count(array_filter($my_jobs, fn($j) => $j["location"] == "Remote")); ?></div>
                <div class="stat-card-label">Remote Jobs</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-number"><?php echo count(array_filter($my_jobs, fn($j) => $j["type"] == "Full-time")); ?></div>
                <div class="stat-card-label">Full-time Jobs</div>
            </div>
        </div>

        <!-- Section header -->
        <div class="section-header" data-aos="fade-up">
            <h2>My Job Listings</h2>
            <a href="post-job.php" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Post New Job
            </a>
        </div>

        <!-- Jobs -->
        <?php if ($total == 0) { ?>
            <div class="card card-body text-center" style="padding:56px;" data-aos="zoom-in">
                <i class="bi bi-briefcase" style="font-size:2.5rem; color:var(--text-light);"></i>
                <h3 class="mt-16" style="font-size:1.2rem;">No jobs posted yet</h3>
                <p class="text-muted mt-8">Start by posting your first job listing.</p>
                <a href="post-job.php" class="btn btn-primary mt-16" style="width:fit-content; margin:16px auto 0;">
                    <i class="bi bi-plus-lg"></i> Post First Job
                </a>
            </div>
        <?php } else { ?>

            <?php foreach ($my_jobs as $i => $job) { ?>
                <div class="job-card" data-aos="fade-up" data-aos-delay="<?php echo $i * 60; ?>">

                    <div class="d-flex align-center gap-12">
                        <div class="user-avatar" style="width:44px;height:44px;font-size:1rem;border-radius:10px;flex-shrink:0;">
                            <?php echo strtoupper(substr($job["company"], 0, 1)); ?>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="job-card-title"><?php echo htmlspecialchars($job["title"]); ?></div>
                            <div class="job-card-meta" style="margin-bottom:0;">
                                <span><i class="bi bi-building"></i><?php echo htmlspecialchars($job["company"]); ?></span>
                                <span><i class="bi bi-geo-alt"></i><?php echo htmlspecialchars($job["location"]); ?></span>
                                <span><i class="bi bi-currency-dollar"></i><?php echo htmlspecialchars($job["salary"]); ?></span>
                            </div>
                        </div>
                        <div class="d-flex gap-8 flex-shrink-0">
                            <a href="edit-job.php?id=<?php echo $job["id"]; ?>" class="btn btn-outline btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="delete-job.php?id=<?php echo $job["id"]; ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this job permanently?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>

                    <div style="font-size:0.75rem; color:var(--text-light); margin-top:12px; padding-left:56px;">
                        <span class="badge badge-muted me-2"><?php echo htmlspecialchars($job["type"]); ?></span>
                        <i class="bi bi-calendar3"></i> <?php echo date("M d, Y", strtotime($job["created_at"])); ?>
                    </div>

                </div>
            <?php } ?>

        <?php } ?>

    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
