<?php 
require_once __DIR__ . '/../../cek_login.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard Reservasi</title>

    <link href="<?= asset('vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="<?= asset('css/sb-admin-2.min.css'); ?>" rel="stylesheet">
    <link href="<?= asset('vendor/datatables/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" type="image/png" href="../img/logonew.webp">

    <style>
    #dataTable tbody tr { cursor: pointer; }
    #dataTable tbody tr.selected { background-color: rgba(196, 65, 33, 0.4) !important; color: #fff; }
    .alert-fixed { position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; }
    
    #dataTable th, #dataTable td {
        text-align: center;
        vertical-align: middle;
    }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">