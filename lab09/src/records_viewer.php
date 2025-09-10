<?php
require __DIR__.'/header.php'; theme_head('BoliRo Medical Records Viewer');
?>
<div class="wrap"><div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2>BoliRo Medical Records Viewer</h2>
    <a class="pill" href="/">Home</a>
  </div>
  <form method="POST" class="row" autocomplete="off">
    <input type="text" id="patient_number" name="patient_number" placeholder="e.g., patient_1001.txt" required>
    <button type="submit">View Record</button>
  </form>
  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $patient_number = $_POST['patient_number'];
      echo "<pre>";
      // INTENTIONALLY VULNERABLE
      system("cat records/" . $patient_number);
      echo "</pre>";
  }
  ?>
  <p class="muted">Sample files: patient_1001.txt, patient_1002.txt, patient_1003.txt</p>
</div></div>
<?php theme_foot(); ?>
