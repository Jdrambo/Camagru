<?php
header("Location: index.php");
session_start();
if(isset($_SESSION)){
    foreach ($_SESSION as $key => $value){
        unset($_SESSION[$key]);
    }
session_unset($_SESSION);
}
?>