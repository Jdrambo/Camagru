<?php
session_start();
header("Location: index.php");
if (isset($_SESSION)){
    foreach ($_SESSION as $key => $value){
        unset($_SESSION[$key]);
    }
    session_unset($_SESSION);
}
?>