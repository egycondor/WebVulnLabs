<?php
require __DIR__.'/../db.php'; start_session();
if(empty($_SESSION['user'])){
  $_SESSION['user']=['username'=>'alice','email'=>'alice@example.com','balance'=>'$5000'];
}
if(isset($_SERVER['HTTP_ORIGIN'])){ header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']); header('Access-Control-Allow-Credentials: true'); header('Vary: Origin'); }
header('Content-Type: application/json');
echo json_encode($_SESSION['user']);
