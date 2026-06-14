<?php
// Check if user is logged in
function is_logged_in() {
    if (!isset($_SESSION["user_id"])) {
        header("Location: ../pages/login.php");
        exit();
    }
}

// Check if user is admin
function is_admin() {
    if (!isset($_SESSION["user_id"]) || $_SESSION["is_admin"] != 1) {
        header("Location: ../index.php");
        exit();
    }
}
?>