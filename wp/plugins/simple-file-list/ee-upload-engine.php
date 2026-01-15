<?php
/**
 * Upload Engine - Standalone endpoint for file uploads
 * This file exists for exploit compatibility with CVE-2025-34085
 * VULNERABLE VERSION - Directory traversal not properly validated
 */

// Load WordPress
$wp_load_paths = [
    __DIR__ . '/../../../wp-load.php',
    __DIR__ . '/../../../../wp-load.php',
    dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php'
];

foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        break;
    }
}

// Load plugin
require_once(__DIR__ . '/ee-simple-file-list.php');

// Initialize plugin classes
global $eeSFL_BASE, $eeSFLU_BASE;

if (!is_object($eeSFL_BASE)) {
    $eeSFL_BASE = new eeSFL_BASE_Class();
    $eeSFL_BASE->eeSFL_Init();
}

if (!is_object($eeSFLU_BASE)) {
    $eeSFLU_BASE = new eeSFL_BASE_UploadClass();
    $eeSFLU_BASE->eeSFL_Init($eeSFL_BASE);
}

// For exploit testing: Force allow uploads (vulnerable behavior)
// In vulnerable version, this setting might be misconfigured or bypassable
if (is_object($eeSFL_BASE) && isset($eeSFL_BASE->eeListSettings)) {
    $eeSFL_BASE->eeListSettings['AllowUploads'] = 'YES';
}

// Set default nonce for vulnerable behavior (bypass check)
// In vulnerable version 6.1.15, this check can be bypassed
if (!isset($_POST['ee-simple-file-list-upload'])) {
    $_POST['ee-simple-file-list-upload'] = wp_create_nonce('ee-simple-file-list-upload');
}

// Set referer to bypass front-end restrictions
if (!isset($_SERVER['HTTP_REFERER'])) {
    $_SERVER['HTTP_REFERER'] = admin_url();
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Set required POST variables
    if (!isset($_POST['eeSFL_ID'])) {
        $_POST['eeSFL_ID'] = '1';
    }
    
    // Fix upload directory path - remove duplicate if present
    if (isset($_POST['eeSFL_FileUploadDir'])) {
        $uploadDir = $_POST['eeSFL_FileUploadDir'];
        // Remove leading slash and ensure it ends with /
        $uploadDir = ltrim($uploadDir, '/');
        if (substr($uploadDir, -1) !== '/') {
            $uploadDir .= '/';
        }
        // If it already contains the full path, extract just the relative part
        if (strpos($uploadDir, 'wp-content/uploads/simple-file-list/') !== false) {
            $uploadDir = str_replace('wp-content/uploads/simple-file-list/', '', $uploadDir);
        }
        $_POST['eeSFL_FileUploadDir'] = $uploadDir;
    }
    
    // Process upload
    if ($eeSFLU_BASE && is_object($eeSFLU_BASE)) {
        $result = $eeSFLU_BASE->eeSFL_FileUploader();
        echo $result;
    } else {
        echo "ERROR - Upload class not initialized";
    }
    
} else {
    echo "ERROR - Invalid request method";
}

