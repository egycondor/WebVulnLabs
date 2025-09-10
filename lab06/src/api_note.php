<?php
error_reporting(0);
if (empty($_COOKIE['user'])){ http_response_code(401); echo '<p class="msg bad">Not logged in.</p>'; exit; }
$mysqli = @new mysqli('db','sqli','sqli','sqli_post');
if($mysqli->connect_error){ http_response_code(500); echo '<p class="msg bad">DB error.</p>'; exit; }

$id = $_POST['id'] ?? '';

// INTENTIONALLY VULNERABLE: post-login SQLi via concatenation
$sql = "SELECT id, title, body, secret_flag FROM notes WHERE id = $id";

$res = $mysqli->query($sql);
if(!$res){
  echo '<div class="msg bad"><b>SQL Error:</b> '.htmlspecialchars($mysqli->error).'</div>';
  echo '<pre class="msg bad" style="white-space:pre-wrap">'.htmlspecialchars($sql).'</pre>';
  exit;
}

echo '<table><thead><tr><th>id</th><th>title</th><th>body</th><th>secret_flag</th></tr></thead><tbody>';
while($row = $res->fetch_assoc()){
  echo '<tr><td>'.htmlspecialchars($row['id']).'</td><td>'.htmlspecialchars($row['title']).'</td><td>'.htmlspecialchars($row['body']).'</td><td>'.htmlspecialchars($row['secret_flag']).'</td></tr>';
}
echo '</tbody></table>';

if ($res->num_rows===0){
  echo '<p class="msg bad">No note found.</p>';
}
