<?php

// Mengatur zona waktu ke Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');

// Menampilkan error untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cek versi PHP minimum
$minPhpVersion = '8.1';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo 'Your PHP version must be ' . $minPhpVersion . ' or higher. Current version: ' . PHP_VERSION;
    exit(1);
}

// Path ke Paths.php
$pathsPath = realpath(FCPATH . '../app/Config/Paths.php');
if (!is_file($pathsPath)) {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo 'Paths.php not found: ' . htmlspecialchars($pathsPath);
    exit(1);
}

// Composer Autoloader
require_once realpath(FCPATH . '../vendor/autoload.php');

// Load konfigurasi path
require_once $pathsPath;
$paths = new Config\Paths();

// Bootstrap CodeIgniter
require_once rtrim($paths->systemDirectory, '\\/ ') . '/Boot.php';

exit(CodeIgniter\Boot::bootWeb($paths));
