<?php
// Start output buffering early to prevent 'headers already sent' during labs
if (!headers_sent()) {
  if (function_exists('ob_start')) { ob_start(); }
}
function start_lab_session(){
  if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
}
function require_login(){ start_lab_session(); if(empty($_SESSION['user'])){ header('Location: /login.php'); exit; } }