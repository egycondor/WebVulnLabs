<?php
// --- keep original logic (just tidied) ---
error_reporting(0);
$conn = mysqli_connect("db", "owel", "owel", "owel"); // service name 'db'
if(!$conn){ die("DB connection error"); }

$name = $_POST['user'] ?? null;
$pass = $_POST['pass'] ?? null;

$result_msg = "";

if ($name !== null) {
  // Original “filter”: only removes single quotes; leaves backslash and everything else
  $name = preg_replace("/'/", '', $name);
  $pass = preg_replace("/'/", '', $pass);

  $query = "SELECT * FROM users WHERE name = '$name' AND password = '$pass'";
  $res   = mysqli_query($conn, $query);
  if(!$res){
    $result_msg = '<p class="msg bad">DB error: '.htmlspecialchars(mysqli_error($conn)).'</p>';
  } else {
    $val = mysqli_fetch_array($res);
    if ($val){
      $result_msg = '<p class="msg ok">Congratz 🎉<br><b>Congs, You bypassed me</b></p>';
    } else {
      $result_msg = '<p class="msg bad">Wrong user or password</p>';
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Bypass The World — SQLi Lab</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0b1222;--panel:#0f172a;--border:#1f2937;--muted:#94a3b8;--acc1:#06b6d4;--acc2:#22d3ee}
    *{box-sizing:border-box} body{margin:0;background:var(--bg);color:#e5e7eb;font-family:Inter,system-ui}
    .wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
    .card{width:100%;max-width:560px;background:var(--panel);border:1px solid var(--border);border-radius:18px;padding:24px;box-shadow:0 10px 30px rgba(0,0,0,.3)}
    h1{margin:0 0 8px} .muted{color:var(--muted);margin:0 0 12px}
    label{display:block;margin:10px 0 6px;color:#cbd5e1}
    input{width:100%;padding:12px;border:1px solid #263046;background:#0b1324;color:#e5e7eb;border-radius:12px}
    button{width:100%;padding:12px;border-radius:12px;border:0;background:linear-gradient(90deg,var(--acc1),var(--acc2));color:#031321;font-weight:700;margin-top:14px}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .msg{padding:12px;border-radius:12px;margin:12px 0}
    .msg.ok{background:#0f2b20;border:1px solid #16a34a;color:#bbf7d0}
    .msg.bad{background:#1a0d10;border:1px solid #7f1d1d;color:#fecaca}
    .hintbtn{margin-top:10px;border:0;background:#0b1324;border:1px solid #243046;color:#a5f3fc;padding:10px 12px;border-radius:10px}
    .hint{margin-top:10px}
    .src{margin-top:12px;color:#a5f3fc;cursor:pointer}
    .foot{margin-top:16px;color:var(--muted);font-size:12px;text-align:center}
    img{max-width:100%;border-radius:12px;border:1px solid #243046}
  </style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <h1>Bypass The World</h1>
    <p class="muted">Subject: Web · SQL Injection (no single quote needed 😉)</p>

    <?php if($result_msg) echo $result_msg; ?>

    <form method="POST" autocomplete="off">
      <label>Username</label>
      <input name="user" placeholder="try a backslash ..." autofocus>
      <label>Password</label>
      <input name="pass" type="password" placeholder='... and something like:  or 1=1 -- -'>
      <button type="submit">Login</button>
    </form>

    <button class="hintbtn" onclick="Hint()">Show hint</button>
    <div id="hint" class="hint"></div>

    <div class="foot">DB: <code>owel</code> · Table: <code>users(name,password)</code></div>
  </div>
</div>

<script>
function Hint(){
  document.getElementById('hint').innerHTML =
    'Bypass Me 😎';
}
</script>
</body>
</html>
