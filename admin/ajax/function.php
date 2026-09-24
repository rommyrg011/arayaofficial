<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host     = "localhost";
$username = "root"; 
$password = "";     
$dbname   = "arayaofficial";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}