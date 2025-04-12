<?php
session_start();
require_once 'bootloader.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    global $kernel;
    if ($kernel->getUsers()->login($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        header('Location: index.php?error=invalid_credentials');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
} 