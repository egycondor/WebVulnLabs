<?php
error_reporting(0);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>SQLi – Find the Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root{--bg:#0b1222;--panel:#0f172a;--border:#1f2937;--muted:#94a3b8;--acc1:#06b6d4;--acc2:#22d3ee}
    *{box-sizing:border-box} body{margin:0;background:var(--bg);color:#e5e7eb;font-family:system-ui,Segoe UI,Roboto}
    .wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
    .card{width:100%;max-width:880px;background:var(--panel);border:1px solid var(--border);border-radius:18px;padding:24px;box-shadow:0 10px 30px rgba(0,0,0,.25)}
    h1{margin:0 0 6px} .muted{color:var(--muted);margin:0 0 16px}
    .searchbar{display:grid;grid-template-columns:1fr auto;gap:10px;margin-bottom:14px}
    input{padding:12px;border:1px solid #263046;background:#0b1324;color:#e5e7eb;border-radius:12px}
    button{padding:12px 16px;border-radius:12px;border:0;background:linear-gradient(90deg,var(--acc1),var(--acc2));color:#031321;font-weight:700}
    table{width:100%;border-collapse:collapse;margin-top:10px}
    th,td{border:1px solid #263046;padding:10px} th{background:#0b1324}
    .msg{padding:10px;border-radius:12px;margin:10px 0}
    .msg.bad{background:#1a0d10;border:1px solid #7f1d1d;color:#fecaca}
    .hintbtn{margin-top:8px;border:0;background:#0b1324;border:1px solid #243046;color:#a5f3fc;padding:10px 12px;border-radius:10px}
    .pill{display:inline-block;padding:6px 10px;border-radius:999px;background:#0b1324;border:1px solid #243046;color:#cbd5e1;font-size:12px}
    code{background:#0b1324;border:1px solid #243046;border-radius:8px;padding:2px 6px}
  </style>
</head>
<body>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <div>
      <h1>Find the Password</h1>
      <p class="muted">Search usernames. The query is vulnerable to SQL injection. Your goal: <span class="pill">leak the admin password</span>.</p>
    </div>
    <div><span class="pill">DB: <code>sqli_lab</code></span></div>
  </div>

  <div class="searchbar">
    <input id="q" placeholder="Try: al">
    <button onclick="runSearch()">Search</button>
  </div>

  <div id="out"></div>

  <button class="hintbtn" onclick="hint()">Show hint</button>
  <div id="hint" style="margin-top:8px"></div>
</div></div>

<script>
async function runSearch(){
  const q = document.getElementById('q').value;
  const r = await fetch('/api_search.php?q='+encodeURIComponent(q));
  const html = await r.text();
  document.getElementById('out').innerHTML = html;
}
function hint(){
  document.getElementById('hint').innerHTML =
    'Try a UNION-based injection to extract hidden columns from <code>users</code>.<br>'+
    'Pattern (MySQL): <code>\' UNION SELECT username,password FROM users -- -</code><br>'+
    'Adjust for column counts via <code>ORDER BY</code> / <code>UNION SELECT NULL,...</code>.';
}
</script>
</body>
</html>
