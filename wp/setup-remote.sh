#!/bin/bash
# Setup script for remote host - ensures plugin is configured correctly

cd /home/ubuntu/vuln-labs/wp

echo "=== Activating Plugin ==="
docker exec wordpress_app php -r "
require '/var/www/html/wp-load.php';
if (!is_plugin_active('simple-file-list/ee-simple-file-list.php')) {
    activate_plugin('simple-file-list/ee-simple-file-list.php');
    echo 'Plugin activated\n';
} else {
    echo 'Plugin already active\n';
}
" 2>&1 | grep -v "Notice:" | grep -v "Deprecated:"

echo ""
echo "=== Configuring Plugin Settings ==="
docker exec wordpress_app php -r "
require '/var/www/html/wp-load.php';

// Get current settings
\$settings = get_option('eeSFL_Settings_1');
if (!\$settings || !is_array(\$settings)) {
    \$settings = array();
}

// Merge with defaults to ensure all required keys exist
global \$eeSFL_BASE;
if (is_object(\$eeSFL_BASE)) {
    \$defaults = \$eeSFL_BASE->DefaultListSettings;
    \$settings = array_merge(\$defaults, \$settings);
}

// Ensure FileListDir is set
if (empty(\$settings['FileListDir'])) {
    \$settings['FileListDir'] = 'wp-content/uploads/simple-file-list/';
}

// Configure critical settings
\$settings['AllowUploads'] = 'YES';
\$settings['FileFormats'] = 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,txt,log,dat';

// Update settings
update_option('eeSFL_Settings_1', \$settings);
echo 'Settings configured\n';
" 2>&1 | grep -v "Notice:" | grep -v "Deprecated:"

echo ""
echo "=== Fixing Uploads Directory Permissions ==="
docker exec wordpress_app bash -c "
mkdir -p /var/www/html/wp-content/uploads/simple-file-list
chown -R www-data:www-data /var/www/html/wp-content/uploads/simple-file-list
chmod -R 755 /var/www/html/wp-content/uploads/simple-file-list
echo 'Permissions fixed'
"

echo ""
echo "=== Verifying Setup ==="
docker exec wordpress_app test -f /var/www/html/wp-content/plugins/simple-file-list/ee-upload-engine.php && echo "✓ Upload engine exists" || echo "✗ Upload engine MISSING"
docker exec wordpress_app test -f /var/www/html/wp-content/plugins/simple-file-list/ee-file-engine.php && echo "✓ File engine exists" || echo "✗ File engine MISSING"
docker exec wordpress_app test -d /var/www/html/wp-content/uploads/simple-file-list && echo "✓ Uploads directory exists" || echo "✗ Uploads directory MISSING"

echo ""
echo "=== Final Settings Verification ==="
docker exec wordpress_app php /var/www/html/verify-settings.php 2>&1 | grep -v "Notice:" | grep -v "Deprecated:" || echo "Running verification..."

echo ""
echo "=== Setup Complete ==="
echo "WordPress URL: http://10.10.0.2:8080"
echo "Plugin: Simple File List v6.1.15 (Vulnerable)"
echo "FileListDir: Fixed"
echo "Ready for CVE-2025-34085 testing"

