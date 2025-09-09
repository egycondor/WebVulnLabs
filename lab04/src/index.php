<?php
if (!empty($_COOKIE['SID'])) {
  header("Location: /verify.php", true, 302);
} else {
  header("Location: /login.php", true, 302);
}
exit;
