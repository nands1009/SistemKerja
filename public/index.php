<?php
// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Enable debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Minimum PHP version
$minPhpVersion = '8.1';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo 'Your PHP version must be ' . $minPhpVersion . ' or higher. Current version: ' . PHP_VERSION;
    exit(1);
}

// Define FCPATH (root of public/)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Path to Paths.php
$pathsPath = realpath(__DIR__ . '/../app/Config/Paths.php');
if (!is_file($pathsPath)) {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo 'Paths.php not found at: ' . htmlspecialchars($pathsPath);
    exit(1);
}

// Composer autoloader
$autoloadPath = realpath(__DIR__ . '/../vendor/autoload.php');
if (!is_file($autoloadPath)) {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo 'Composer autoloader not found at: ' . htmlspecialchars($autoloadPath);
    exit(1);
}
require_once $autoloadPath;

// Load Paths
require_once $pathsPath;
$paths = new Config\Paths();

// Bootstrap CodeIgniter
require_once rtrim($paths->systemDirectory, '\\/ ') . '/Boot.php';
exit(CodeIgniter\Boot::bootWeb($paths));
