<?php require_once "includes/header.php"; ?>
<?php require_once "config/database.php"; ?>

<?php
$pdo = connectDB();
$stmt = $pdo->query("SELECT * FROM jobs ORDER BY created_at DESC LIMIT 6");
$featured = $stmt->fetchAll();
$stmt2 = $pdo->query("SELECT COUNT(*) FROM jobs");
$total_jobs = $stmt2->fetchColumn();
$stmt3 = $pdo->query("SELECT COUNT(*) FROM users");
$total_users = $stmt3->fetchColumn();
?>

<!-- ── HERO ── -->
<section class="hero">
    <div class="hero-grid"></div>
    <div class="container hero-content">

        <div class="hero-eyebrow" data-aos="fade-up">
            <i class="bi bi-lightning-charge-fill"></i>
            <?php echo $total_jobs; ?> Jobs Available Now
        </div>

        <h1 data-aos="fade-up" data-aos-delay="100">
            Find Your Next<br><span class="highlight">Developer Role</span>
        </h1>

        <p data-aos="fade-up" data-aos-delay="200">
            Browse remote and on-site developer jobs from top tech companies. Post a job opening in minutes.
        </p>

        <!-- Search bar -->
        <div data-aos="fade-up" data-aos-delay="300">
            <div class="search-wrap">
                <form method="GET" action="<?php echo BASE_URL; ?>/pages/jobs.php">
                    <div class="search-row">
                        <div class="search-field">
                            <label><i class="bi bi-search"></i> Keyword</label>
                            <input type="text" name="keyword" class="form-control" placeholder="e.g. PHP, Laravel, React...">
                        </div>
                        <div class="search-field">
                            <label><i class="bi bi-tag"></i> Job Type</label>
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Freelance">Freelance</option>
                            </select>
                        </div>
                        <div class="search-field">
                            <label><i class="bi bi-geo-alt"></i> Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Remote, London...">
                        </div>
                        <div class="search-btn">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-search"></i> Search Jobs
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>

<!-- ── STATS BAR ── -->
<div class="stats-bar">
    <div class="stats-inner">
        <div class="stat-item">
            <i class="bi bi-briefcase-fill" style="color:var(--primary)"></i>
            <span><strong><?php echo $total_jobs; ?></strong> Jobs Posted</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <i class="bi bi-people-fill" style="color:var(--accent)"></i>
            <span><strong><?php echo $total_users; ?></strong> Developers</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <i class="bi bi-globe" style="color:var(--warning)"></i>
            <span><strong>100%</strong> Free to Use</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <i class="bi bi-lightning-charge-fill" style="color:var(--primary)"></i>
            <span>Post in <strong>2 minutes</strong></span>
        </div>
    </div>
</div>

<!-- ── FEATURED JOBS ── -->
<section class="page-wrap">
    <div class="container">

        <div class="section-header" data-aos="fade-up">
            <h2>Featured Jobs</h2>
            <a href="<?php echo BASE_URL; ?>/pages/jobs.php" class="btn btn-outline btn-sm">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <?php if (count($featured) == 0) { ?>
            <div class="card card-body text-center" style="padding:48px;">
                <i class="bi bi-briefcase" style="font-size:2rem; color:var(--text-light);"></i>
                <p class="mt-16 text-muted">No jobs posted yet. Be the first!</p>
                <a href="<?php echo BASE_URL; ?>/pages/post-job.php" class="btn btn-primary mt-16" style="width:fit-content; margin:16px auto 0;">Post a Job</a>
            </div>
        <?php } else { ?>

            <div class="row g-3">
                <?php foreach ($featured as $i => $job) { ?>
                    <div class="col-12 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $i * 80; ?>">
                        <div class="job-card h-100">
                            <a href="<?php echo BASE_URL; ?>/pages/job-detail.php?id=<?php echo $job["id"]; ?>" class="job-card-link"></a>

                            <div class="d-flex align-center gap-12 mb-8">
                                <div class="user-avatar" style="width:42px; height:42px; font-size:1rem; border-radius:10px; flex-shrink:0;">
                                    <?php echo strtoupper(substr($job["company"], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="job-card-title"><?php echo htmlspecialchars($job["title"]); ?></div>
                                    <div style="font-size:0.82rem; color:var(--text-muted);"><?php echo htmlspecialchars($job["company"]); ?></div>
                                </div>
                            </div>

                            <div class="job-card-meta">
                                <span><i class="bi bi-geo-alt"></i><?php echo htmlspecialchars($job["location"]); ?></span>
                                <span><i class="bi bi-clock"></i><?php echo htmlspecialchars($job["type"]); ?></span>
                            </div>

                            <div class="job-card-desc"><?php echo htmlspecialchars($job["description"]); ?></div>

                            <div class="d-flex flex-wrap gap-8">
                                <span class="badge badge-primary"><i class="bi bi-currency-dollar"></i><?php echo htmlspecialchars($job["salary"]); ?></span>
                                <?php if ($job["location"] == "Remote") { ?>
                                    <span class="badge badge-accent"><i class="bi bi-wifi"></i> Remote</span>
                                <?php } ?>
                                <span class="badge badge-muted"><?php echo htmlspecialchars($job["type"]); ?></span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="text-center mt-32" data-aos="fade-up">
                <a href="<?php echo BASE_URL; ?>/pages/jobs.php" class="btn btn-primary btn-lg">
                    Browse All Jobs <i class="bi bi-arrow-right"></i>
                </a>
            </div>

        <?php } ?>

    </div>
</section>

<?php require_once "includes/footer.php"; ?>
