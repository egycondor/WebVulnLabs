<?php
error_reporting(0);
$mysqli = @new mysqli('db','sqli','sqli','sqli_lab');
if($mysqli->connect_error){ http_response_code(500); echo '<p class="msg bad">DB error.</p>'; exit; }

$q = $_GET['q'] ?? '';
// INTENTIONALLY VULNERABLE
$sql = "SELECT username, bio FROM public_profiles WHERE username LIKE '%$q%'";

$res = $mysqli->query($sql);
if(!$res){
  echo '<div class="msg bad"><b>SQL Error:</b> '.htmlspecialchars($mysqli->error).'</div>';
  echo '<pre class="msg bad" style="white-space:pre-wrap">'.htmlspecialchars($sql).'</pre>';
  exit;
}

echo '<table><thead><tr><th>username</th><th>bio</th></tr></thead><tbody>';
while($row = $res->fetch_assoc()){
  echo '<tr><td>'.htmlspecialchars($row['username']).'</td><td>'.htmlspecialchars($row['bio']).'</td></tr>';
}
echo '</tbody></table>';
