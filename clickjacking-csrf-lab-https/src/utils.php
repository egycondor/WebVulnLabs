<?php
function start_lab_session(){
  if(session_status() !== PHP_SESSION_ACTIVE){ session_start(); }
}
function require_login(){
  start_lab_session();
  if(empty($_SESSION['user'])){
    header('Location: /login.php'); exit;
  }
}
