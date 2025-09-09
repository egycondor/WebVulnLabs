<?php
error_reporting(E_ALL); ini_set('display_errors', 1);

$DB_HOST='db'; $DB_USER='priv'; $DB_PASS='priv'; $DB_NAME='privlab';
$conn = new mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);
if($conn->connect_error){ die('DB connection failed: '.$conn->connect_error); }

function weak_hash($s){ return md5($s); }

function get_user_by_username($u){
  global $conn; $u = $conn->real_escape_string($u);
  $res = $conn->query("SELECT * FROM users WHERE username='$u' LIMIT 1");
  return ($res && $res->num_rows===1)? $res->fetch_assoc(): null;
}

function get_user_by_id($id){
  global $conn; $id = (int)$id;
  $res = $conn->query("SELECT * FROM users WHERE id=$id LIMIT 1");
  return ($res && $res->num_rows===1)? $res->fetch_assoc(): null;
}
