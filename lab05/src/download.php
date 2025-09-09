<?php
require_once __DIR__.'/config.php';
$u = $_COOKIE['user'] ?? null;
if(!$u){ header("Location: /login.php"); exit; }

// VULN: trusts arbitrary filename param, no ownership check
$file = $_GET['file'] ?? '';
$path = "/var/www/html/files/$file"; // (no validation in this simple lab)

if(!preg_match('/^invoice_\d+\.pdf$/', $file)){
  http_response_code(400); echo "Invalid filename format."; exit;
}

if(!file_exists($path)){
  // create a dummy file on-the-fly for demo
  @mkdir("/var/www/html/files");
  file_put_contents($path, "Invoice placeholder for $file\n");
}

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"$file\"");
readfile($path);
