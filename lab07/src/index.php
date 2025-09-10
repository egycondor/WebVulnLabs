<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Acme Shop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root{--bg:#0b1222;--panel:#0f172a;--border:#1f2937;--muted:#94a3b8;--acc1:#06b6d4;--acc2:#22d3ee}
    *{box-sizing:border-box} body{margin:0;background:var(--bg);color:#e5e7eb;font-family:system-ui,Segoe UI,Roboto}
    .wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
    .card{width:100%;max-width:900px;background:var(--panel);border:1px solid var(--border);border-radius:18px;padding:24px;box-shadow:0 10px 30px rgba(0,0,0,.25)}
    h1{margin:.2em 0 .5em} .muted{color:var(--muted)}
    a{color:#a5f3fc;text-decoration:none}
    .btn{display:inline-block;padding:12px 16px;border-radius:12px;border:0;background:linear-gradient(90deg,var(--acc1),var(--acc2));color:#031321;font-weight:700}
    .pill{display:inline-block;padding:6px 10px;border-radius:999px;background:#0b1324;border:1px solid #243046;color:#cbd5e1;font-size:12px}
  </style>
</head>
<body>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h1>Acme Shop</h1>
    <span class="pill">DB: shop</span>
  </div>
  <p class="muted">Browse our products.</p>
  <p><a class="btn" href="/products.php">Open Products</a></p>
</div></div>
</body>
</html>
