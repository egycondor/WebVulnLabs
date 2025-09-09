<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$DB_HOST='db'; $DB_USER='mfa'; $DB_PASS='mfa'; $DB_NAME='mfalab';
$conn = new mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);
if($conn->connect_error){ die('DB connection failed: '.$conn->connect_error); }

function weak_hash($s){ return md5($s); }

function create_session($username){
  global $conn;
  $sid = bin2hex(random_bytes(16));                      // 32 hex
  $otp = str_pad((string)random_int(0,999999),6,'0',STR_PAD_LEFT); // 6-digit numeric
  $u = $conn->real_escape_string($username);
  $s = $conn->real_escape_string($sid);
  $o = $conn->real_escape_string($otp);
  $conn->query("INSERT INTO sessions (sid, username, otp_code, mfa_ok) VALUES('$s','$u','$o',0)");
  setcookie('SID', $sid, 0, '/'); // no HttpOnly/Secure on purpose (training)
}

function get_session(){
  global $conn;
  $sid = $_COOKIE['SID'] ?? '';
  if(!$sid) return null;
  $s = $conn->real_escape_string($sid);
  $res = $conn->query("SELECT * FROM sessions WHERE sid='$s' LIMIT 1");
  return ($res && $res->num_rows===1) ? $res->fetch_assoc() : null;
}

function mark_mfa_ok($sid){
  global $conn;
  $s = $conn->real_escape_string($sid);
  $conn->query("UPDATE sessions SET mfa_ok=1 WHERE sid='$s' LIMIT 1");
}
