<?php
session_start();
require_once __DIR__ . "/../config/constants.php";
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> — Find Developer Jobs</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Our CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/responsive.css">
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar" id="mainNav">
    <div class="container">

        <!-- Brand -->
        <a href="<?php echo BASE_URL; ?>/index.php" class="navbar-brand">
            Dev<span>Board</span><span class="brand-dot"></span>
        </a>

        <!-- Nav Links -->
        <ul class="navbar-links" id="navLinks">
            <li><a href="<?php echo BASE_URL; ?>/index.php"><i class="bi bi-house"></i> Home</a></li>
            <li><a href="<?php echo BASE_URL; ?>/pages/jobs.php"><i class="bi bi-briefcase"></i> Browse Jobs</a></li>

            <?php if (isset($_SESSION["user_id"])) { ?>
                <li><a href="<?php echo BASE_URL; ?>/pages/dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/my-applications.php"><i class="bi bi-file-earmark-text"></i> My Applications</a></li>
                <?php if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] == 1) { ?>
                    <li><a href="<?php echo BASE_URL; ?>/pages/admin/index.php"><i class="bi bi-shield-check"></i> Admin</a></li>
                <?php } ?>
            <?php } ?>
        </ul>

        <!-- Right side -->
        <div class="navbar-right">

            <!-- Theme Toggle -->
            <button class="theme-toggle" id="themeToggle" title="Toggle dark/light mode" aria-label="Toggle theme"></button>

            <?php if (isset($_SESSION["user_id"])) { ?>
                <!-- User info -->
                <div class="navbar-user d-none d-md-flex">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION["user_name"], 0, 1)); ?>
                    </div>
                    <span><?php echo htmlspecialchars(explode(" ", $_SESSION["user_name"])[0]); ?></span>
                </div>
                <a href="<?php echo BASE_URL; ?>/pages/post-job.php" class="btn-post d-none d-md-inline-flex">
                    <i class="bi bi-plus-lg"></i> Post Job
                </a>
                <a href="<?php echo BASE_URL; ?>/pages/logout.php" class="btn btn-sm" style="color:rgba(255,255,255,0.6); background:rgba(255,255,255,0.08); border-radius:var(--radius-sm);">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            <?php } else { ?>
                <a href="<?php echo BASE_URL; ?>/pages/login.php" class="btn btn-sm" style="color:rgba(255,255,255,0.75); background:rgba(255,255,255,0.08); border-radius:var(--radius-sm);">Login</a>
                <a href="<?php echo BASE_URL; ?>/pages/register.php" class="btn-post">Get Started</a>
            <?php } ?>

            <!-- Hamburger -->
            <button class="navbar-toggler" id="navToggler" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
        </div>

    </div>
</nav>
<!-- ── END NAVBAR ── -->
