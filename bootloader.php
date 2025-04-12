<?php
session_start();

// System constants
define('SYSTEM_ROOT', __DIR__);
define('KERNEL_PATH', SYSTEM_ROOT . '/kernel');
define('SYSTEM_PATH', SYSTEM_ROOT . '/system');
define('APPS_PATH', SYSTEM_ROOT . '/apps');

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load kernel
try {
    require_once KERNEL_PATH . '/kernel.php';
    require_once KERNEL_PATH . '/fs.php';
    require_once KERNEL_PATH . '/users.php';
    require_once KERNEL_PATH . '/processes.php';
    require_once KERNEL_PATH . '/log.php';
    require_once KERNEL_PATH . '/syscalls.php';
    
    // Initialize kernel
    $kernel = new Kernel();
    $kernel->boot();
    
    // Load desktop GUI
    require_once SYSTEM_PATH . '/desktop.php';
    $desktop = new Desktop();
    $desktop->render();
    
} catch (Exception $e) {
    // Show panic screen if boot fails
    require_once SYSTEM_PATH . '/panic.php';
    panic("Boot failed: " . $e->getMessage());
} 