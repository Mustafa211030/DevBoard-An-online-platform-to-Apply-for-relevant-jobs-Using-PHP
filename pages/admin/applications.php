<?php
require_once "../../includes/header.php";
require_once "../../config/database.php";
require_once "../../includes/auth.php";
?>

<?php is_admin(); ?>

<?php
$pdo = connectDB();

// ── Handle status update ──
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_status"])) {

    // CSRF check
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION["csrf_token"]) {
        die("Invalid request.");
    }

    $app_id    = (int)$_POST["app_id"];
    $new_status = $_POST["new_status"];
    $allowed_statuses = ["pending", "reviewed", "shortlisted", "rejected"];

    if (in_array($new_status, $allowed_statuses)) {
        $stmt = $pdo->prepare("UPDATE applications SET status = :status WHERE id = :id");
        $stmt->execute([":status" => $new_status, ":id" => $app_id]);
    }

    header("Location: applications.php");
    exit();
}

// ── Filter by job (optional) ──
$filter_job = isset($_GET["job_id"]) ? (int)$_GET["job_id"] : 0;

// ── Get all applications with job + user info ──
if ($filter_job > 0) {
    $stmt = $pdo->prepare("
        SELECT applications.*,
               jobs.title  as job_title,
               jobs.company,
               jobs.location,
               users.name  as applicant_name,
               users.email as applicant_email
        FROM applications
        LEFT JOIN jobs  ON applications.job_id  = jobs.id
        LEFT JOIN users ON applications.user_id = users.id
        WHERE applications.job_id = :job_id
        ORDER BY applications.applied_at DESC
    ");
    $stmt->execute([":job_id" => $filter_job]);
} else {
    $stmt = $pdo->query("
        SELECT applications.*,
               jobs.title  as job_title,
               jobs.company,
               jobs.location,
               users.name  as applicant_name,
               users.email as applicant_email
        FROM applications
        LEFT JOIN jobs  ON applications.job_id  = jobs.id
        LEFT JOIN users ON applications.user_id = users.id
        ORDER BY applications.applied_at DESC
    ");
}

$all_applications = $stmt->fetchAll();

// ── Get all jobs for filter dropdown ──
$jobs_stmt = $pdo->query("SELECT id, title, company FROM jobs ORDER BY title ASC");
$all_jobs  = $jobs_stmt->fetchAll();

// ── Count by status ──
$counts = ["pending" => 0, "reviewed" => 0, "shortlisted" => 0, "rejected" => 0];
foreach ($all_applications as $app) {
    if (isset($counts[$app["status"]])) {
        $counts[$app["status"]]++;
    }
}

// CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
?>

<div class="dashboard-header">
    <div class="container">
        <h1 class="dashboard-title"><i class="bi bi-file-earmark-person"></i> Job <span>Applications</span></h1>
        <p class="dashboard-subtitle">Review CVs and manage application statuses</p>
    </div>
</div>

<div class="page-wrap">
    <div class="container">

        <!-- Stats row -->
        <div class="stats-grid" data-aos="fade-up" style="grid-template-columns:repeat(4,1fr);">
            <div class="stat-card">
                <div class="stat-card-number"><?php echo count($all_applications); ?></div>
                <div class="stat-card-label">Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-number" style="color:var(--warning);"><?php echo $counts["pending"]; ?></div>
                <div class="stat-card-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-number" style="color:var(--accent);"><?php echo $counts["shortlisted"]; ?></div>
                <div class="stat-card-label">Shortlisted</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-number" style="color:var(--danger);"><?php echo $counts["rejected"]; ?></div>
                <div class="stat-card-label">Rejected</div>
            </div>
        </div>

        <!-- Admin tabs -->
        <div class="admin-nav-tabs" data-aos="fade-up">
            <a href="index.php">All Jobs</a>
            <a href="users.php">All Users</a>
            <a href="applications.php" class="active">Applications</a>
        </div>

        <!-- Filter by job -->
        <div class="card card-body mb-24" data-aos="fade-up" style="padding:16px 20px;">
            <form method="GET" action="" class="d-flex align-center gap-12 flex-wrap">
                <label style="font-size:0.85rem; font-weight:600; color:var(--text-muted); white-space:nowrap;">
                    <i class="bi bi-funnel"></i> Filter by Job:
                </label>
                <select name="job_id" class="form-control" style="max-width:320px;" onchange="this.form.submit()">
                    <option value="0">All Jobs</option>
                    <?php foreach ($all_jobs as $j) { ?>
                        <option value="<?php echo $j["id"]; ?>" <?php if ($filter_job == $j["id"]) echo "selected"; ?>>
                            <?php echo htmlspecialchars($j["title"]); ?> — <?php echo htmlspecialchars($j["company"]); ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if ($filter_job > 0) { ?>
                    <a href="applications.php" class="btn btn-outline btn-sm">Clear Filter</a>
                <?php } ?>
            </form>
        </div>

        <!-- Applications list -->
        <?php if (count($all_applications) == 0) { ?>
            <div class="card card-body text-center" style="padding:56px;" data-aos="zoom-in">
                <i class="bi bi-inbox" style="font-size:2.5rem; color:var(--text-light);"></i>
                <h3 class="mt-16" style="font-size:1.2rem;">No applications yet</h3>
                <p class="text-muted mt-8">Applications will appear here when candidates apply.</p>
            </div>
        <?php } else { ?>

            <?php foreach ($all_applications as $i => $app) { ?>

                <div class="card mb-16" data-aos="fade-up" data-aos-delay="<?php echo min($i * 50, 300); ?>">
                    <div class="card-body">

                        <!-- Top row -->
                        <div class="d-flex align-center gap-12 flex-wrap">

                            <!-- Avatar -->
                            <div class="user-avatar" style="width:48px;height:48px;font-size:1.1rem;border-radius:10px;flex-shrink:0; background:linear-gradient(135deg,var(--navy-soft),var(--navy-mid));">
                                <?php echo strtoupper(substr($app["applicant_name"], 0, 1)); ?>
                            </div>

                            <!-- Applicant info -->
                            <div style="flex:1; min-width:0;">
                                <div style="font-family:var(--font-display); font-weight:700; font-size:1rem; color:var(--text);">
                                    <?php echo htmlspecialchars($app["full_name"]); ?>
                                </div>
                                <div style="font-size:0.82rem; color:var(--text-muted); margin-top:3px; display:flex; flex-wrap:wrap; gap:12px;">
                                    <span><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($app["email"]); ?></span>
                                    <span><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($app["phone"]); ?></span>
                                </div>
                            </div>

                            <!-- Status badge -->
                            <?php
                            $statusMap = [
                                "pending"     => ["badge-warning", "bi-hourglass-split",  "Pending"],
                                "reviewed"    => ["badge-primary", "bi-eye-fill",          "Reviewed"],
                                "shortlisted" => ["badge-accent",  "bi-star-fill",         "Shortlisted"],
                                "rejected"    => ["badge-danger",  "bi-x-circle-fill",     "Rejected"],
                            ];
                            $s = $statusMap[$app["status"]] ?? $statusMap["pending"];
                            ?>
                            <span class="badge <?php echo $s[0]; ?>" style="flex-shrink:0;">
                                <i class="bi <?php echo $s[1]; ?>"></i> <?php echo $s[2]; ?>
                            </span>

                        </div>

                        <!-- Job they applied for -->
                        <div style="margin-top:14px; padding:12px 14px; background:var(--bg-alt); border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                            <div style="font-size:0.85rem; color:var(--text-muted);">
                                <i class="bi bi-briefcase me-1" style="color:var(--primary);"></i>
                                Applied for: <strong style="color:var(--text);"><?php echo htmlspecialchars($app["job_title"]); ?></strong>
                                at <strong style="color:var(--text);"><?php echo htmlspecialchars($app["company"]); ?></strong>
                                &nbsp;&bull;&nbsp; <?php echo htmlspecialchars($app["location"]); ?>
                            </div>
                            <div style="font-size:0.78rem; color:var(--text-light);">
                                <i class="bi bi-calendar3"></i> <?php echo date("M d, Y · h:i A", strtotime($app["applied_at"])); ?>
                            </div>
                        </div>

                        <!-- Cover letter (if any) -->
                        <?php if (!empty($app["cover_letter"])) { ?>
                            <div style="margin-top:12px;">
                                <p style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted); margin-bottom:6px;">
                                    <i class="bi bi-chat-quote"></i> Cover Letter
                                </p>
                                <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6; padding:12px 14px; background:var(--bg-input); border-radius:var(--radius-sm); border:1px solid var(--border-soft);">
                                    <?php echo nl2br(htmlspecialchars($app["cover_letter"])); ?>
                                </p>
                            </div>
                        <?php } ?>

                        <!-- Actions row -->
                        <div style="margin-top:16px; padding-top:14px; border-top:1px solid var(--border-soft); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">

                            <!-- Download CV button -->
                            <a href="download-cv.php?id=<?php echo $app["id"]; ?>"
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-download"></i> Download CV
                            </a>

                            <!-- Status update form -->
                            <form method="POST" action="" class="d-flex align-center gap-8">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION["csrf_token"]; ?>">
                                <input type="hidden" name="app_id"     value="<?php echo $app["id"]; ?>">
                                <input type="hidden" name="update_status" value="1">

                                <select name="new_status" class="form-control" style="font-size:0.85rem; padding:7px 12px; width:auto;">
                                    <option value="pending"     <?php if ($app["status"]=="pending")     echo "selected"; ?>>Pending</option>
                                    <option value="reviewed"    <?php if ($app["status"]=="reviewed")    echo "selected"; ?>>Reviewed</option>
                                    <option value="shortlisted" <?php if ($app["status"]=="shortlisted") echo "selected"; ?>>Shortlisted</option>
                                    <option value="rejected"    <?php if ($app["status"]=="rejected")    echo "selected"; ?>>Rejected</option>
                                </select>

                                <button type="submit" class="btn btn-outline btn-sm">
                                    <i class="bi bi-check-lg"></i> Update
                                </button>
                            </form>

                        </div>

                    </div>
                </div>

            <?php } ?>

        <?php } ?>

    </div>
</div>

<?php require_once "../../includes/footer.php"; ?>
