<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$koneksi = mysqli_connect('localhost', 'root', '', 'arayaofficial');

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$project_dir = dirname($_SERVER['SCRIPT_NAME']);
$project_dir = str_replace('\\', '/', $project_dir);

$modul_folders = ['admin','reservasi', 'rental', 'karyawan'];

if (in_array(basename($project_dir), $modul_folders)) {
    $project_dir = dirname($project_dir);
}

$project_root = rtrim($project_dir, '/') . '/';
define('BASE_URL', $protocol . $host . $project_root);

function asset($path) {
    return BASE_URL . ltrim($path, '/');
}