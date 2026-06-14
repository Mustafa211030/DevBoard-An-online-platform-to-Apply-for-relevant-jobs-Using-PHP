<?php
define("DB_HOST", "sql100.infinityfree.com");
define("DB_USER", "if0_41658001");   // your actual username
define("DB_PASS", "cadet665");         // password you created
define("DB_NAME", "if0_41658001_devboard");    // your actual db name

function connectDB() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
            DB_USER,
            DB_PASS
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        die("Something went wrong. Please try again later.");
    }
}
?>