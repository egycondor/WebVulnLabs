<?php
function db(){
  static $conn;
  if(!$conn){
    $conn = new mysqli('db','vw','vw','vwapp');
  }
  return $conn;
}
function start_session(){ if(session_status()!==PHP_SESSION_ACTIVE){ session_start(); } }
