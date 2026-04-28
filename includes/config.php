<?php
declare(strict_types=1);

// Load .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Remove surrounding quotes if present
            if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                $value = substr($value, 1, -1);
            }
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Set error reporting
error_reporting(E_ALL);
$appEnv = $_ENV['APP_ENV'] ?? 'production';
if ($appEnv !== 'development') {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../php_errors.log');
} else {
    ini_set('display_errors', '1');
}

// Define constants
define('APP_ENV', $appEnv);
define('APP_NAME', $_ENV['APP_NAME'] ?? 'BMI Calculator');
define('APP_URL', $_ENV['APP_URL'] ?? '');
?>