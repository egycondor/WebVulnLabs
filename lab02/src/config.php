<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$DB_HOST = 'db';
$DB_USER = 'sess';
$DB_PASS = 'sess';
$DB_NAME = 'sessionlab';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) { die('DB connection failed: ' . $conn->connect_error); }

function weak_hash($s) { return md5($s); }