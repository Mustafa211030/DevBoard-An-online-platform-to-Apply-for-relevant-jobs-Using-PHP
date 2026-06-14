<?php
require_once "../../includes/header.php";
require_once "../../config/database.php";
require_once "../../includes/auth.php";
?>

<?php is_admin(); ?>

<?php
$pdo = connectDB();
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$all_users = $stmt->fetchAll();
?>

<div class="dashboard-header">
    <div class="container">
        <h1 class="dashboard-title"><i class="bi bi-people-fill"></i> All <span>Users</span></h1>
        <p class="dashboard-subtitle">View and manage registered users</p>
    </div>
</div>

<div class="page-wrap">
    <div class="container">

        <!-- Tabs -->
        <div class="admin-nav-tabs" data-aos="fade-up">
            <a href="index.php">All Jobs</a>
            <a href="users.php" class="active">All Users</a>
            <a href="applications.php">Applications</a>
        </div>

        <!-- Table -->
        <div class="table-wrap" data-aos="fade-up">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_users as $user) { ?>
                        <tr>
                            <td><span class="badge badge-muted">#<?php echo $user["id"]; ?></span></td>
                            <td>
                                <div class="d-flex align-center gap-8">
                                    <div class="user-avatar" style="width:30px;height:30px;font-size:0.75rem;flex-shrink:0;">
                                        <?php echo strtoupper(substr($user["name"], 0, 1)); ?>
                                    </div>
                                    <strong><?php echo htmlspecialchars($user["name"]); ?></strong>
                                </div>
                            </td>
                            <td style="color:var(--text-muted);"><?php echo htmlspecialchars($user["email"]); ?></td>
                            <td>
                                <?php if ($user["is_admin"] == 1) { ?>
                                    <span class="badge badge-warning"><i class="bi bi-shield-fill"></i> Admin</span>
                                <?php } else { ?>
                                    <span class="badge badge-muted"><i class="bi bi-person-fill"></i> User</span>
                                <?php } ?>
                            </td>
                            <td style="color:var(--text-muted); font-size:0.85rem;"><?php echo date("M d, Y", strtotime($user["created_at"])); ?></td>
                            <td>
                                <?php if ($user["is_admin"] != 1) { ?>
                                    <a href="delete-user.php?id=<?php echo $user["id"]; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete this user and all their jobs?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                <?php } else { ?>
                                    <span style="color:var(--text-light); font-size:0.85rem;">—</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php require_once "../../includes/footer.php"; ?>
