<?php require_once "../includes/header.php"; ?>
<?php require_once "../config/database.php"; ?>
<?php require_once "../includes/auth.php"; ?>

<?php is_logged_in(); ?>

<?php
$pdo = connectDB();

// Get job
$job_id = isset($_GET["job_id"]) ? (int)$_GET["job_id"] : 0;

$stmt = $pdo->prepare("SELECT jobs.*, users.name as poster_name FROM jobs LEFT JOIN users ON jobs.user_id = users.id WHERE jobs.id = :id");
$stmt->execute([":id" => $job_id]);
$job = $stmt->fetch();

if (!$job) {
    echo "<div class='page-wrap'><div class='container'><p>Job not found.</p></div></div>";
    require_once "../includes/footer.php";
    exit();
}

// Check: already applied?
$stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = :job_id AND user_id = :user_id");
$stmt->execute([":job_id" => $job_id, ":user_id" => $_SESSION["user_id"]]);
$already_applied = $stmt->fetch();

$error_message   = "";
$success_message = "";

// Pre-fill from session
$full_name    = $_SESSION["user_name"];
$email        = $_SESSION["user_email"];
$phone        = "";
$cover_letter = "";

// CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST" && !$already_applied) {

    // CSRF check
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION["csrf_token"]) {
        die("Invalid request. Please go back and try again.");
    }

    // Get text fields
    $full_name    = trim($_POST["full_name"]);
    $email        = trim($_POST["email"]);
    $phone        = trim($_POST["phone"]);
    $cover_letter = trim($_POST["cover_letter"]);

    // Validate
    if ($full_name == "") {
        $error_message = "Full name is required";

    } elseif ($email == "") {
        $error_message = "Email is required";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address";

    } elseif ($phone == "") {
        $error_message = "Phone number is required";

    } elseif (!isset($_FILES["cv"]) || $_FILES["cv"]["error"] == UPLOAD_ERR_NO_FILE) {
        $error_message = "Please upload your CV";

    } else {

        $file       = $_FILES["cv"];
        $file_name  = $file["name"];
        $file_size  = $file["size"];
        $file_tmp   = $file["tmp_name"];
        $file_error = $file["error"];
        $ext        = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if ($file_error !== UPLOAD_ERR_OK) {
            $error_message = "File upload failed. Please try again.";

        } elseif (!in_array($ext, ALLOWED_EXTENSIONS)) {
            $error_message = "Only PDF, DOC, and DOCX files are allowed.";

        } elseif ($file_size > MAX_FILE_SIZE) {
            $error_message = "File is too large. Maximum size is 5MB.";

        } elseif (!is_uploaded_file($file_tmp)) {
            $error_message = "Invalid file upload.";

        } else {

            $safe_filename = "cv_" . $_SESSION["user_id"] . "_" . $job_id . "_" . time() . "_" . bin2hex(random_bytes(6)) . "." . $ext;
            $upload_path   = UPLOADS_DIR . $safe_filename;

            if (!is_dir(UPLOADS_DIR)) {
                mkdir(UPLOADS_DIR, 0755, true);
            }

            if (move_uploaded_file($file_tmp, $upload_path)) {

                $stmt = $pdo->prepare("
                    INSERT INTO applications (job_id, user_id, full_name, email, phone, cover_letter, cv_filename, cv_path)
                    VALUES (:job_id, :user_id, :full_name, :email, :phone, :cover_letter, :cv_filename, :cv_path)
                ");

                $stmt->execute([
                    ":job_id"       => $job_id,
                    ":user_id"      => $_SESSION["user_id"],
                    ":full_name"    => $full_name,
                    ":email"        => $email,
                    ":phone"        => $phone,
                    ":cover_letter" => $cover_letter,
                    ":cv_filename"  => $safe_filename,
                    ":cv_path"      => $upload_path
                ]);

                $success_message = "Application submitted successfully!";
                $already_applied = true;

            } else {
                $error_message = "Failed to save file. Please check folder permissions.";
            }
        }
    }
}
?>

<!-- Page Header -->
<div class="detail-header">
    <div class="container">
        <a href="job-detail.php?id=<?php echo $job_id; ?>" class="btn btn-sm mb-20" style="color:rgba(255,255,255,0.6); background:rgba(255,255,255,0.08); border-radius:var(--radius-sm);">
            <i class="bi bi-arrow-left"></i> Back to Job
        </a>
        <div class="d-flex align-center gap-16 flex-wrap">
            <div class="user-avatar" style="width:56px;height:56px;font-size:1.3rem;border-radius:12px;flex-shrink:0;">
                <?php echo strtoupper(substr($job["company"], 0, 1)); ?>
            </div>
            <div>
                <h1 style="color:#fff; font-size:1.8rem;">Apply for this Role</h1>
                <div class="detail-header-meta">
                    <span><i class="bi bi-briefcase"></i> <?php echo htmlspecialchars($job["title"]); ?></span>
                    <span><i class="bi bi-building"></i> <?php echo htmlspecialchars($job["company"]); ?></span>
                    <span><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($job["location"]); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-wrap">
    <div class="container">
        <div class="row g-4 justify-content-center">

            <!-- Left: Form -->
            <div class="col-12 col-lg-7">

                <?php if ($already_applied && $success_message == "") { ?>

                    <!-- Already applied -->
                    <div class="card card-body text-center" style="padding:56px;" data-aos="zoom-in">
                        <div style="width:72px;height:72px;background:rgba(6,214,160,0.12);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                            <i class="bi bi-check-circle-fill" style="font-size:2rem;color:var(--accent);"></i>
                        </div>
                        <h3>Already Applied</h3>
                        <p class="text-muted mt-8">You have already submitted an application for this job.</p>
                        <div class="d-flex gap-12 justify-content-center mt-24">
                            <a href="jobs.php" class="btn btn-outline">Browse More Jobs</a>
                            <a href="my-applications.php" class="btn btn-primary">My Applications</a>
                        </div>
                    </div>

                <?php } elseif ($success_message != "") { ?>

                    <!-- Success -->
                    <div class="card card-body text-center" style="padding:56px;" data-aos="zoom-in">
                        <div style="width:72px;height:72px;background:rgba(6,214,160,0.12);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                            <i class="bi bi-check-circle-fill" style="font-size:2rem;color:var(--accent);"></i>
                        </div>
                        <h3>Application Submitted!</h3>
                        <p class="text-muted mt-8">Your CV has been sent to <strong><?php echo htmlspecialchars($job["company"]); ?></strong>. Good luck!</p>
                        <div class="d-flex gap-12 justify-content-center mt-24">
                            <a href="jobs.php" class="btn btn-outline">Browse More Jobs</a>
                            <a href="my-applications.php" class="btn btn-primary">View My Applications</a>
                        </div>
                    </div>

                <?php } else { ?>

                    <!-- Application form -->
                    <div class="form-card" style="max-width:100%;" data-aos="fade-up">

                        <h2 style="margin-bottom:6px;">Your Application</h2>
                        <p class="subtitle">Fill in your details and attach your CV below</p>

                        <?php if ($error_message != "") { ?>
                            <div class="alert alert-error">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php } ?>

                        <form method="POST" action="" enctype="multipart/form-data">

                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION["csrf_token"]; ?>">

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="bi bi-person"></i> Full Name</label>
                                        <input type="text" name="full_name" class="form-control"
                                               value="<?php echo htmlspecialchars($full_name); ?>"
                                               placeholder="Your full name" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="bi bi-envelope"></i> Email Address</label>
                                        <input type="email" name="email" class="form-control"
                                               value="<?php echo htmlspecialchars($email); ?>"
                                               placeholder="you@example.com" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label><i class="bi bi-telephone"></i> Phone Number</label>
                                        <input type="text" name="phone" class="form-control"
                                               value="<?php echo htmlspecialchars($phone); ?>"
                                               placeholder="+92 300 1234567" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label><i class="bi bi-chat-text"></i> Cover Letter <span style="color:var(--text-light); font-weight:400;">(optional)</span></label>
                                        <textarea name="cover_letter" class="form-control" style="height:160px;"
                                                  placeholder="Tell the employer why you are a great fit..."><?php echo htmlspecialchars($cover_letter); ?></textarea>
                                    </div>
                                </div>

                                <!-- CV Upload Drop Zone -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label><i class="bi bi-file-earmark-person"></i> Upload CV / Resume</label>

                                        <div id="dropZone" style="
                                            position: relative;
                                            border: 2px dashed var(--border);
                                            border-radius: var(--radius-sm);
                                            padding: 32px;
                                            text-align: center;
                                            cursor: pointer;
                                            transition: var(--transition);
                                            background: var(--bg-input);
                                        ">
                                            <i class="bi bi-cloud-upload" style="font-size:2rem; color:var(--primary); display:block; margin-bottom:10px;"></i>
                                            <p style="font-weight:600; color:var(--text); margin-bottom:4px;">Drag & drop your CV here</p>
                                            <p style="font-size:0.82rem; color:var(--text-muted);">or click to browse files</p>
                                            <p style="font-size:0.75rem; color:var(--text-light); margin-top:8px;">PDF, DOC, DOCX — max 5MB</p>

                                            <input type="file" name="cv" id="cvInput" accept=".pdf,.doc,.docx"
                                                   style="position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%;" required>
                                        </div>

                                        <!-- File preview -->
                                        <div id="filePreview" style="display:none; margin-top:12px; padding:12px 16px; background:rgba(6,214,160,0.08); border:1px solid rgba(6,214,160,0.2); border-radius:var(--radius-sm); align-items:center; gap:10px;">
                                            <i class="bi bi-file-earmark-check-fill" style="color:var(--accent); font-size:1.2rem;"></i>
                                            <span id="fileName" style="font-size:0.9rem; font-weight:600; color:var(--text);"></span>
                                            <span id="fileSize" style="font-size:0.8rem; color:var(--text-muted); margin-left:auto;"></span>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary btn-block mt-16" style="padding:14px;">
                                <i class="bi bi-send-fill"></i> Submit Application
                            </button>

                        </form>
                    </div>

                <?php } ?>

            </div>

            <!-- Right: Job summary -->
            <div class="col-12 col-lg-4">
                <div class="apply-card" style="position:sticky; top:90px;" data-aos="fade-left">
                    <h3>Job Summary</h3>
                    <p style="color:rgba(255,255,255,0.5); font-size:0.82rem; margin-bottom:0;">You are applying for:</p>

                    <div style="margin-top:16px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.08);">
                        <div style="font-family:var(--font-display); font-weight:700; color:#fff; font-size:1.05rem; margin-bottom:12px;">
                            <?php echo htmlspecialchars($job["title"]); ?>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:8px;">
                            <span style="color:rgba(255,255,255,0.55); font-size:0.85rem;"><i class="bi bi-building me-2"></i><?php echo htmlspecialchars($job["company"]); ?></span>
                            <span style="color:rgba(255,255,255,0.55); font-size:0.85rem;"><i class="bi bi-geo-alt me-2"></i><?php echo htmlspecialchars($job["location"]); ?></span>
                            <span style="color:rgba(255,255,255,0.55); font-size:0.85rem;"><i class="bi bi-cash-stack me-2"></i><?php echo htmlspecialchars($job["salary"]); ?></span>
                            <span style="color:rgba(255,255,255,0.55); font-size:0.85rem;"><i class="bi bi-clock me-2"></i><?php echo htmlspecialchars($job["type"]); ?></span>
                        </div>
                    </div>

                    <div style="margin-top:20px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.08);">
                        <div style="display:flex; align-items:center; gap:8px; color:rgba(255,255,255,0.4); font-size:0.8rem;">
                            <i class="bi bi-shield-lock-fill" style="color:var(--accent);"></i>
                            Your data is secure and only visible to the employer
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const dropZone    = document.getElementById('dropZone');
const cvInput     = document.getElementById('cvInput');
const filePreview = document.getElementById('filePreview');
const fileName    = document.getElementById('fileName');
const fileSize    = document.getElementById('fileSize');

function showFile(file) {
    if (!file) return;
    const sizeMB = (file.size / 1024 / 1024).toFixed(2);
    fileName.textContent = file.name;
    fileSize.textContent = sizeMB + " MB";
    filePreview.style.display = "flex";
    dropZone.style.borderColor = "var(--accent)";
    dropZone.style.background  = "rgba(6,214,160,0.04)";
}

cvInput.addEventListener('change', () => showFile(cvInput.files[0]));

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = "var(--primary)";
    dropZone.style.background  = "rgba(14,165,233,0.04)";
});

dropZone.addEventListener('dragleave', () => {
    dropZone.style.borderColor = "var(--border)";
    dropZone.style.background  = "var(--bg-input)";
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        cvInput.files = dt.files;
        showFile(file);
    }
});
</script>

<?php require_once "../includes/footer.php"; ?>