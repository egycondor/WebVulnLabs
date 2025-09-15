<?php
require __DIR__.'/header.php'; theme_head('Vulnerable Suite (MySQL)');
?>
<div class="wrap"><div class="card">
  <h1>Vulnerable Web App Suite (MySQL)</h1>
  <p class="muted">Realistic DB-backed app with intentionally broken features.</p>
  <div class="grid">
    <a href="/auth/login.php">Auth (SQLi) + CSRF Delete</a>
    <a href="/sqli/products.php">SQLi: Product Search</a>
    <a href="/xss/reflected.php">XSS: Reflected</a>
    <a href="/cmd/ping.php">Command Injection</a>
    <a href="/upload/index.php">Unrestricted File Upload</a>
    <a href="/lfi/view.php?file=readme.txt">LFI Viewer</a>
    <a href="/idor/order.php?id=1">IDOR: Orders</a>
    <a href="/cors/account.php">CORS Misconfig</a>
    <a href="/ssrf/fetch.php?url=http://127.0.0.1/">SSRF Fetcher</a>
    <a href="/xxe/upload.php">XXE Parser</a>
  </div>
</div></div>
<?php theme_foot(); ?>
