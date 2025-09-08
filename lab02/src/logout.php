<?php
setcookie("role", "", time()-3600, "/");
header("Location: /login.php");
exit;