<?php
/**
 * File Engine - Standalone endpoint for file operations (rename, delete, etc.)
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

// Initialize plugin
global $eeSFL_BASE;
if (!is_object($eeSFL_BASE)) {
    $eeSFL_BASE = new eeSFL_BASE_Class();
    $eeSFL_BASE->eeSFL_Init();
}

// Handle file rename operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eeFileAction'])) {
    
    // Parse the action (format: "Rename|newfilename.php")
    $action = $_POST['eeFileAction'];
    
    if (strpos($action, 'Rename|') === 0) {
        $newName = substr($action, 7); // Remove "Rename|"
        
        // Set required POST variables for the file editor
        $_POST['eeFileAction'] = 'Edit';
        $_POST['eeFileNameNew'] = $newName;
        
        if (!isset($_POST['eeFileName'])) {
            $_POST['eeFileName'] = $_POST['eeFileOld'] ?? '';
        }
        
        if (!isset($_POST['eeSFL_ID'])) {
            $_POST['eeSFL_ID'] = '1';
        }
        
        // Create nonce for vulnerable version (bypass check)
        $_REQUEST['eeSecurity'] = wp_create_nonce('ee-sfl-manage-files');
        $_POST['eeSecurity'] = $_REQUEST['eeSecurity'];
        
        // Set referer to bypass front-end check
        $_SERVER['HTTP_REFERER'] = admin_url();
        
        // Bypass extension check for exploit testing (vulnerable behavior)
        // In vulnerable version, this check might be missing or bypassable
        // We'll do a direct file rename instead of using the editor function
        $oldFilePath = ABSPATH . $eeSFL_BASE->eeListSettings['FileListDir'] . $_POST['eeFileName'];
        $newFilePath = ABSPATH . $eeSFL_BASE->eeListSettings['FileListDir'] . $newName;
        
        if (file_exists($oldFilePath)) {
            if (rename($oldFilePath, $newFilePath)) {
                echo "SUCCESS - File renamed to: " . $newName;
            } else {
                echo "ERROR - Could not rename file";
            }
        } else {
            // Fallback to using the editor function
            $result = eeSFL_BASE_FileEditor();
            echo $result;
        }
        
    } else {
        echo "ERROR - Unsupported action";
    }
    
} else {
    echo "ERROR - Invalid request";
}

