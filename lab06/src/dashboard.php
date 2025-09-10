<?php
error_reporting(0);
if (empty($_COOKIE['user'])){ header("Location: /index.php"); exit; }
$user = htmlspecialchars($_COOKIE['user']);
include __DIR__.'/header.php';
?>
<div class="wrap"><div class="card">
  <div class="top">
    <h2>Welcome, <?= $user ?></h2>
    <div><a href="/logout.php">Logout</a></div>
  </div>
  <p class="muted">Lookup your notes by <b>ID</b>. The backend uses a POST call and concatenates your input into SQL (intentionally vulnerable).</p>

  <form id="f" onsubmit="return false;" class="row" style="margin-top:8px">
    <div>
      <label>Note ID</label>
      <input id="note_id" placeholder="e.g., 1">
    </div>
    <div style="align-self:end">
      <button onclick="doLookup()">Lookup</button>
    </div>
  </form>

  <div id="out"></div>

  <div class="msg ok" style="margin-top:12px">
    Goal: exfiltrate the admin's secret flag from the <code>notes</code> table by abusing the post-login endpoint.
  </div>
</div></div>
<script>
async function doLookup(){
  const id = document.getElementById('note_id').value;
  const r = await fetch('/api_note.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'id='+encodeURIComponent(id)
  });
  const html = await r.text();
  document.getElementById('out').innerHTML = html;
}
</script>
</body></html>
