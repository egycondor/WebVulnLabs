<?php
require __DIR__.'/utils.php';
require_login();
start_lab_session();

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
  header("Access-Control-Allow-Origin: $origin");
  header("Access-Control-Allow-Credentials: true");
  header("Vary: Origin");
}
header("Content-Type: application/json");

echo json_encode([
  "user"    => $_SESSION['user']['name'],
  "email"   => $_SESSION['user']['email'],
  "balance" => $_SESSION['user']['balance'],
  "tip"     => "This data should never be shared cross-origin."
]);
