<?php
session_start();

// Delete everything in the session
session_destroy();

// Send back to homepage
header("Location: ../index.php");
exit();
?>