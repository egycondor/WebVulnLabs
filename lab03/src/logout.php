<?php
// expire the cookie immediately
setcookie("user", "", time()-3600, "/");

// redirect to login page
header("Location: /login.php");
exit;
