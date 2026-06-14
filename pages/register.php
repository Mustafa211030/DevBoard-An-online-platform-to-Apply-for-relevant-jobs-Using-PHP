<?php
require_once "../includes/header.php";
require_once "../config/database.php";
?>

<?php
$name = "";
$email = "";
$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST["name"];
    $email    = $_POST["email"];
    $password = $_POST["password"];
    $confirm  = $_POST["confirm"];

    if ($name == "") {
        $error_message = "Name is required";
    } elseif ($email == "") {
        $error_message = "Email is required";
    } elseif ($password == "") {
        $error_message = "Password is required";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters";
    } elseif ($password != $confirm) {
        $error_message = "Passwords do not match";
    } else {
        $pdo = connectDB();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([":email" => $email]);
        $existing_user = $stmt->fetch();

        if ($existing_user) {
            $error_message = "This email is already registered";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->execute([":name" => $name, ":email" => $email, ":password" => $hashed_password]);
            $success_message = "Account created! You can now login.";
            $name  = "";
            $email = "";
        }
    }
}
?>

<div style="min-height:calc(100vh - var(--nav-height)); display:flex; align-items:center; padding:48px 16px; background:var(--bg);">
    <div class="form-card" style="width:100%;" data-aos="zoom-in">

        <div class="text-center mb-24">
            <div class="user-avatar" style="width:56px; height:56px; font-size:1.4rem; margin:0 auto 16px; background:linear-gradient(135deg,var(--accent),var(--accent-dark));">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <h2>Create account</h2>
            <p class="subtitle">Join DevBoard — it's completely free</p>
        </div>

        <?php if ($error_message != "") { ?>
            <div class="alert alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <?php echo $error_message; ?>
            </div>
        <?php } ?>

        <?php if ($success_message != "") { ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <?php echo $success_message; ?>
                <a href="login.php" style="font-weight:600; margin-left:6px;">Sign in now</a>
            </div>
        <?php } ?>

        <form method="POST" action="">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" placeholder="John Doe" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" placeholder="you@example.com" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm" class="form-control" placeholder="Repeat password" required>
            </div>

            <button type="submit" class="btn btn-accent btn-block mt-8">
                <i class="bi bi-person-check-fill"></i> Create Account
            </button>

        </form>

        <p class="text-center mt-24" style="font-size:0.9rem; color:var(--text-muted);">
            Already have an account? <a href="login.php" style="font-weight:600;">Sign in</a>
        </p>

    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
