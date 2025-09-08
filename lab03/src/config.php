<?php
error_reporting(E_ALL); ini_set('display_errors', 1);

$DB_HOST='db'; $DB_USER='pw'; $DB_PASS='pw'; $DB_NAME='pwlab';
$conn = new mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);
if($conn->connect_error){ die('DB connection failed: '.$conn->connect_error); }

function weak_hash($s){ return md5($s); }

/* ---- FLAWED server-side "policy" (intentionally weak) ----
 * Accepts passwords if:
 *   - length >= 6, AND
 *   - (has uppercase OR has digit OR has special)
 * Missing: blacklist checks; multi-class AND; strong length; history; etc.
 */
function flawed_policy_accepts($pwd){
  $len = strlen($pwd) >= 6;
  $hasUpper = preg_match('/[A-Z]/', $pwd);
  $hasDigit = preg_match('/\d/', $pwd);
  $hasSpecial = preg_match('/[^A-Za-z0-9]/', $pwd);
  return $len && ($hasUpper || $hasDigit || $hasSpecial);
}

/* Verbose rule breakdown (used for messages) */
function rule_feedback($pwd){
  $msgs = [];
  if(strlen($pwd) < 12) $msgs[] = "Must be at least 12 characters (UI policy)";
  if(!preg_match('/[A-Z]/',$pwd)) $msgs[] = "Add at least one uppercase letter";
  if(!preg_match('/[a-z]/',$pwd)) $msgs[] = "Add at least one lowercase letter";
  if(!preg_match('/\d/',$pwd))     $msgs[] = "Add at least one digit";
  if(!preg_match('/[^A-Za-z0-9]/',$pwd)) $msgs[] = "Add at least one special character";
  return $msgs;
}

function find_user($u){
  global $conn;
  $u = $conn->real_escape_string($u);
  $res = $conn->query("SELECT * FROM users WHERE username='$u' LIMIT 1");
  return ($res && $res->num_rows===1) ? $res->fetch_assoc() : null;
}

function set_password($u,$pwd){
  global $conn;
  $u = $conn->real_escape_string($u);
  $h = weak_hash($pwd);
  $conn->query("UPDATE users SET password_md5='$h', pwd_changed_at=NOW() WHERE username='$u' LIMIT 1");
}
