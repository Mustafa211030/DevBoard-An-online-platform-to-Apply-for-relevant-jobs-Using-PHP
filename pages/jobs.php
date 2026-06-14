<?php require_once "../includes/header.php"; ?>
<?php require_once "../config/database.php"; ?>

<?php
$pdo = connectDB();
$keyword  = $_GET["keyword"]  ?? "";
$type     = $_GET["type"]     ?? "";
$location = $_GET["location"] ?? "";

$sql = "SELECT * FROM jobs WHERE 1=1";
if ($keyword  != "") $sql .= " AND (title LIKE :keyword OR company LIKE :keyword)";
if ($type     != "") $sql .= " AND type = :type";
if ($location != "") $sql .= " AND location LIKE :location";
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
if ($keyword  != "") $stmt->bindValue(":keyword",  "%" . $keyword . "%");
if ($type     != "") $stmt->bindValue(":type",      $type);
if ($location != "") $stmt->bindValue(":location", "%" . $location . "%");
$stmt->execute();
$jobs = $stmt->fetchAll();
$total = count($jobs);
?>

<!-- ── HERO / SEARCH ── -->
<section class="hero" style="padding: 56px 0 48px;">
    <div class="hero-grid"></div>
    <div class="container hero-content">

        <h1 data-aos="fade-up" style="font-size:clamp(1.6rem,4vw,2.4rem);">
            Browse <span class="highlight">Developer Jobs</span>
        </h1>
        <p data-aos="fade-up" data-aos-delay="100">Filter by keyword, type, or location to find your perfect role.</p>

        <div data-aos="fade-up" data-aos-delay="200">
            <div class="search-wrap">
                <form method="GET" action="">
                    <div class="search-row">
                        <div class="search-field">
                            <label><i class="bi bi-search"></i> Keyword</label>
                            <input type="text" name="keyword" class="form-control" placeholder="Title or company..." value="<?php echo htmlspecialchars($keyword); ?>">
                        </div>
                        <div class="search-field">
                            <label><i class="bi bi-tag"></i> Job Type</label>
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <?php foreach (["Full-time","Part-time","Contract","Freelance"] as $t) { ?>
                                    <option value="<?php echo $t; ?>" <?php if ($t == $type) echo "selected"; ?>><?php echo $t; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="search-field">
                            <label><i class="bi bi-geo-alt"></i> Location</label>
                            <input type="text" name="location" class="form-control" placeholder="City or Remote..." value="<?php echo htmlspecialchars($location); ?>">
                        </div>
                        <div class="search-btn">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>

<!-- ── RESULTS ── -->
<div class="page-wrap">
    <div class="container">

        <div class="section-header" data-aos="fade-up">
            <div>
                <h2>
                    <?php if ($keyword != "" || $type != "" || $location != "") { ?>
                        Search Results
                    <?php } else { ?>
                        All Jobs
                    <?php } ?>
                </h2>
                <p class="results-count mt-8">
                    Found <strong><?php echo $total; ?></strong> job<?php echo $total != 1 ? "s" : ""; ?>
                    <?php if ($keyword != "" || $type != "" || $location != "") { ?>
                        &nbsp;&mdash;&nbsp;<a href="jobs.php" style="font-size:0.85rem;">Clear filters <i class="bi bi-x"></i></a>
                    <?php } ?>
                </p>
            </div>
        </div>

        <?php if ($total == 0) { ?>
            <div class="card card-body text-center" style="padding:56px;" data-aos="zoom-in">
                <i class="bi bi-search" style="font-size:2.5rem; color:var(--text-light);"></i>
                <h3 class="mt-16" style="font-size:1.2rem;">No jobs found</h3>
                <p class="text-muted mt-8">Try different search terms or clear your filters.</p>
                <a href="jobs.php" class="btn btn-outline mt-16" style="width:fit-content; margin:16px auto 0;">View All Jobs</a>
            </div>
        <?php } else { ?>

            <?php foreach ($jobs as $i => $job) { ?>
                <div class="job-card" data-aos="fade-up" data-aos-delay="<?php echo min($i * 60, 400); ?>">
                    <a href="job-detail.php?id=<?php echo $job["id"]; ?>" class="job-card-link"></a>

                    <div class="d-flex align-center gap-12 mb-8">
                        <div class="user-avatar" style="width:46px; height:46px; font-size:1.1rem; border-radius:10px; flex-shrink:0;">
                            <?php echo strtoupper(substr($job["company"], 0, 1)); ?>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="job-card-title"><?php echo htmlspecialchars($job["title"]); ?></div>
                            <div class="job-card-meta" style="margin-bottom:0;">
                                <span><i class="bi bi-building"></i><?php echo htmlspecialchars($job["company"]); ?></span>
                                <span><i class="bi bi-geo-alt"></i><?php echo htmlspecialchars($job["location"]); ?></span>
                                <span><i class="bi bi-clock"></i><?php echo htmlspecialchars($job["type"]); ?></span>
                            </div>
                        </div>
                        <div class="d-none d-md-flex flex-wrap gap-8" style="flex-shrink:0;">
                            <span class="badge badge-primary"><i class="bi bi-currency-dollar"></i><?php echo htmlspecialchars($job["salary"]); ?></span>
                            <?php if ($job["location"] == "Remote") { ?>
                                <span class="badge badge-accent"><i class="bi bi-wifi"></i> Remote</span>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="job-card-desc"><?php echo htmlspecialchars($job["description"]); ?></div>

                    <div class="d-flex flex-wrap gap-8 d-md-none">
                        <span class="badge badge-primary"><i class="bi bi-currency-dollar"></i><?php echo htmlspecialchars($job["salary"]); ?></span>
                        <?php if ($job["location"] == "Remote") { ?>
                            <span class="badge badge-accent"><i class="bi bi-wifi"></i> Remote</span>
                        <?php } ?>
                    </div>

                    <div style="font-size:0.75rem; color:var(--text-light); margin-top:12px;">
                        <i class="bi bi-calendar3"></i> Posted <?php echo date("M d, Y", strtotime($job["created_at"])); ?>
                    </div>
                </div>
            <?php } ?>

        <?php } ?>

    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
