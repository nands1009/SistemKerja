<?php

// Mengatur zona waktu ke Asia/Jakarta (WIB)
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('Asia/Jakarta');
}

// Menampilkan error untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
*---------------------------------------------------------------
* CHECK PHP VERSION
*---------------------------------------------------------------
*/
$minPhpVersion = '8.1'; 
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION
    );
    header('HTTP/1.1 503 Service Unavailable', true, 503);
    echo $message;
    exit(1);
}

/*
*---------------------------------------------------------------
* SET THE CURRENT DIRECTORY
*---------------------------------------------------------------
*/
// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
*---------------------------------------------------------------
* BOOTSTRAP THE APPLICATION
*---------------------------------------------------------------
* This process sets up the path constants, loads and registers
* our autoloader, along with Composer's, loads our constants
* and fires up an environment-specific bootstrapping.
*/

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
$pathsPath = __DIR__ . '/../app/Config/Paths.php';

require __DIR__ . '/../vendor/autoload.php'; 
// ^^^ Change this line if you move your application folder

$pathsPath = __DIR__ . '/../app/Config/Paths.php'; 
require $pathsPath; 
$paths = new Config\Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

// Start the application
exit(CodeIgniter\Boot::bootWeb($paths));
