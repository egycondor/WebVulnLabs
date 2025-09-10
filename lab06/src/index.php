<?php
error_reporting(0);
if (!empty($_COOKIE['user'])) { header("Location: /dashboard.php"); exit; }
?>
<?php include __DIR__.'/header.php'; ?>
<div class="wrap"><div class="card">
  <div class="top"><h1>Post-login SQLi</h1><span class="pill">DB: sqli_post</span></div>
  <p class="muted">Login, then use the search panel. The post-login endpoint is vulnerable to SQL injection.</p>
  <form method="POST" action="/login.php">
    <div class="row">
      <div>
        <label>Username</label>
        <input name="username" placeholder="alice">
      </div>
      <div>
        <label>Password</label>
        <input name="password" type="password" placeholder="alice123">
      </div>
    </div>
    <div style="margin-top:12px"><button type="submit">Sign in</button></div>
  </form>
  <p class="muted" style="margin-top:8px">Users: alice/alice123 · bob/bob123 · admin/Summer2025!</p>
</div></div>
</body></html>
