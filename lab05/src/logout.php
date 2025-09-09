<?php
setcookie('user','',time()-3600,'/');
setcookie('role','',time()-3600,'/');
header("Location: /login.php");
exit;
