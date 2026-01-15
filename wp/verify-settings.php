<?php
/**
 * Verify and fix plugin settings
 */
require_once('/var/www/html/wp-load.php');

// Get current settings
$settings = get_option('eeSFL_Settings_1');

if (!$settings || !is_array($settings)) {
    $settings = array();
}

// Merge with defaults to ensure all required keys exist
global $eeSFL_BASE;
if (!is_object($eeSFL_BASE)) {
    $eeSFL_BASE = new eeSFL_BASE_Class();
    $eeSFL_BASE->eeSFL_Init();
}

$defaults = $eeSFL_BASE->DefaultListSettings;
$settings = array_merge($defaults, $settings);

// Ensure FileListDir is set
if (empty($settings['FileListDir'])) {
    $settings['FileListDir'] = 'wp-content/uploads/simple-file-list/';
}

// Ensure other critical settings
$settings['AllowUploads'] = 'YES';
$settings['FileFormats'] = 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,txt,log,dat';

// Update settings
update_option('eeSFL_Settings_1', $settings);

// Verify
$verify = get_option('eeSFL_Settings_1');
echo "FileListDir: " . ($verify['FileListDir'] ?? 'MISSING') . "\n";
echo "AllowUploads: " . ($verify['AllowUploads'] ?? 'NOT SET') . "\n";
echo "Settings verified and fixed!\n";

