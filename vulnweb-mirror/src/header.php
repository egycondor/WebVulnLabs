<?php
function theme_head($title='Vulnerable WEB App – Mirror'){
  echo '<!doctype html><html><head><meta charset="utf-8"><title>'.htmlspecialchars($title).'</title>';
  echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
  echo '<style>
  :root{--bg:#0b1222;--panel:#0f172a;--border:#1f2937;--muted:#94a3b8;--acc1:#06b6d4;--acc2:#22d3ee}
  *{box-sizing:border-box} body{margin:0;background:var(--bg);color:#e5e7eb;font-family:system-ui,Segoe UI,Roboto}
  .wrap{min-height:100dvh;display:grid;place-items:center;padding:24px}
  .card{width:100%;max-width:1100px;background:var(--panel);border:1px solid var(--border);border-radius:18px;padding:24px;box-shadow:0 10px 30px rgba(0,0,0,.25)}
  h1,h2{margin:.2em 0 .6em} .muted{color:var(--muted)}
  .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px}
  a{color:#a5f3fc;text-decoration:none;border:1px solid #263046;padding:12px;border-radius:12px;background:#0b1324}
  </style></head><body>';
}
function theme_foot(){ echo '</body></html>'; }
