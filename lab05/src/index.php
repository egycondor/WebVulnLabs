<?php
if (!empty($_COOKIE['user'])) {
  header("Location: /home.php", true, 302);
} else {
  header("Location: /login.php", true, 302);
}
exit;
