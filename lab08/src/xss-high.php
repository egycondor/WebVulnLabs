<?php
header("Content-Security-Policy: default-src 'self'; img-src 'self' https://dummyimage.com; style-src 'unsafe-inline' 'self'");
require __DIR__.'/header.php'; theme_head('XSS – High');
$q_raw = $_GET['q'] ?? '';
$q_html = htmlspecialchars($q_raw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$q_url = rawurlencode($q_raw);
?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2>Search (High)</h2>
    <a class="pill" href="/">Home</a>
  </div>
  <form method="GET" class="row" autocomplete="off">
    <input name="q" placeholder="Search..." value="<?php echo $q_html; ?>">
    <button type="submit">Go</button>
  </form>

  <div class="out">
    <?php if(isset($_GET['q'])): ?>
      <div>Results for: <b><?php echo $q_html; ?></b></div>
      <div style="margin-top:10px">
        <img src="https://dummyimage.com/120x40/0b1324/e5e7eb&text=<?php echo $q_url; ?>" alt="<?php echo $q_html; ?>">
      </div>
    <?php endif; ?>
  </div>
  <p class="muted" style="margin-top:10px">This level encodes output for each context and sets a basic CSP.</p>
</div></div>
<?php theme_foot(); ?>
