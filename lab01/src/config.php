<?php
// super loud errors (lab only)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$DB_HOST = 'db';
$DB_USER = 'bruter';
$DB_PASS = 'bruter';
$DB_NAME = 'brutelab';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die('DB connection failed: ' . $conn->connect_error);
}

// helper: super weak hash wrapper (MD5 on purpose)
function weak_hash($s) { return md5($s); }