<?php
require_once "../includes/header.php";
require_once "../config/database.php";
?>

<?php
$email = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST["email"];
    $password = $_POST["password"];

    if ($email == "") {
        $error_message = "Email is required";
    } elseif ($password == "") {
        $error_message = "Password is required";
    } else {
        $pdo = connectDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([":email" => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            $error_message = "No account found with this email";
        } elseif (!password_verify($password, $user["password"])) {
            $error_message = "Wrong password";
        } else {
            $_SESSION["user_id"]   = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"]= $user["email"];
            $_SESSION["is_admin"]  = $user["is_admin"];
            header("Location: ../index.php");
            exit();
        }
    }
}
?>

<div style="min-height:calc(100vh - var(--nav-height)); display:flex; align-items:center; padding:48px 16px; background:var(--bg);">
    <div class="form-card" style="width:100%;" data-aos="zoom-in">

        <div class="text-center mb-24">
            <div class="user-avatar" style="width:56px; height:56px; font-size:1.4rem; margin:0 auto 16px;">
                <i class="bi bi-person-fill"></i>
            </div>
            <h2>Welcome back</h2>
            <p class="subtitle">Sign in to your DevBoard account</p>
        </div>

        <?php if ($error_message != "") { ?>
            <div class="alert alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <?php echo $error_message; ?>
            </div>
        <?php } ?>

        <form method="POST" action="">

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" placeholder="you@example.com" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Your password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-8">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </button>

        </form>

        <p class="text-center mt-24" style="font-size:0.9rem; color:var(--text-muted);">
            No account yet? <a href="register.php" style="font-weight:600;">Create one free</a>
        </p>

    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
