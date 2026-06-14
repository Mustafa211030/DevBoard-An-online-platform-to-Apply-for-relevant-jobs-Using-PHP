<?php
require_once "../../includes/header.php";
require_once "../../config/database.php";
require_once "../../includes/auth.php";
?>

<?php is_admin(); ?>

<?php
$pdo = connectDB();
$stmt = $pdo->query("SELECT COUNT(*) FROM jobs"); $total_jobs = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM users"); $total_users = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT jobs.*, users.name as poster_name FROM jobs LEFT JOIN users ON jobs.user_id = users.id ORDER BY jobs.created_at DESC");
$all_jobs = $stmt->fetchAll();
?>

<div class="dashboard-header">
    <div class="container">
        <h1 class="dashboard-title"><i class="bi bi-shield-check"></i> Admin <span>Panel</span></h1>
        <p class="dashboard-subtitle">Manage all jobs and users</p>
    </div>
</div>

<div class="page-wrap">
    <div class="container">

        <!-- Stats -->
        <div class="stats-grid" data-aos="fade-up">
            <div class="stat-card">
                <div class="stat-card-number"><?php echo $total_jobs; ?></div>
                <div class="stat-card-label">Total Jobs</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-number"><?php echo $total_users; ?></div>
                <div class="stat-card-label">Total Users</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="admin-nav-tabs" data-aos="fade-up">
            <a href="index.php" class="active">All Jobs</a>
            <a href="users.php">All Users</a>
            <a href="applications.php">Applications</a>
        </div>

        <!-- Table -->
        <div class="table-wrap" data-aos="fade-up">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Posted By</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_jobs as $job) { ?>
                        <tr>
                            <td><span class="badge badge-muted">#<?php echo $job["id"]; ?></span></td>
                            <td><strong><?php echo htmlspecialchars($job["title"]); ?></strong></td>
                            <td><?php echo htmlspecialchars($job["company"]); ?></td>
                            <td><?php echo htmlspecialchars($job["poster_name"] ?? "—"); ?></td>
                            <td><span class="badge badge-primary"><?php echo htmlspecialchars($job["type"]); ?></span></td>
                            <td style="color:var(--text-muted); font-size:0.85rem;"><?php echo date("M d, Y", strtotime($job["created_at"])); ?></td>
                            <td>
                                <a href="delete-job.php?id=<?php echo $job["id"]; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this job?')">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php require_once "../../includes/footer.php"; ?>
