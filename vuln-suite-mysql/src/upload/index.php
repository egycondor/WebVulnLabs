<?php
require __DIR__.'/../header.php'; theme_head('Unrestricted Upload');
@mkdir(__DIR__.'/files',0777,true);
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['f'])){
  $name = $_FILES['f']['name'];
  $dest = __DIR__.'/files/'.$name; // VULN: no validation
  if(move_uploaded_file($_FILES['f']['tmp_name'],$dest)){ $msg='Uploaded to /upload/files/'.$name; } else { $msg='Upload failed'; }
}
?>
<div class="wrap"><div class="card">
  <h2>Upload</h2>
  <form method="POST" enctype="multipart/form-data">
    <input type="file" name="f"><button>Send</button>
  </form>
  <?php if($msg): ?><div class="pill"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
  <p><a href="/upload/files/">Browse uploads</a></p>
</div></div>
<?php theme_foot(); ?>
