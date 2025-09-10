<?php
require __DIR__.'/header.php'; theme_head('XSS – Low');
$q = $_GET['q'] ?? '';
?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2>Search (Low)</h2>
    <a class="pill" href="/">Home</a>
  </div>
  <form method="GET" class="row" autocomplete="off">
    <input name="q" placeholder="Search..." value="<?php echo isset($_GET['q'])?$_GET['q']:''; ?>">
    <button type="submit">Go</button>
  </form>

  <div class="out">
    <?php if(isset($_GET['q'])): ?>
      <div>Results for: <b><?php echo $q; ?></b></div>
      <div style="margin-top:10px">
        <img src="https://dummyimage.com/120x40/0b1324/e5e7eb&text=<?php echo $q; ?>" alt="<?php echo $q; ?>">
      </div>
    <?php endif; ?>
  </div>
  <p class="muted" style="margin-top:10px">This level reflects your input directly into the page without encoding.</p>
</div></div>
<?php theme_foot(); ?>
