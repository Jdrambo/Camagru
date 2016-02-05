<?php
session_start();
function loadClass($name){
	require($name.".php");
}
spl_autoload_register("loadClass");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset = "utf-8">
<link rel = "stylesheet" href = "css/style.css">
</head>
<body>
<div class = "container">
	<?php include('header.php');?>
    <form class = "standard-form" action = "connexion.php" method = "post">
        <h2 class = "title-form">Connexion</h2>
        <input class = "field" type = "text" name = "login" placeholder = "Identifiant">
        <input class = "field" type = "password" name = "password" placeholder = "Mot de passe">
        <button class = "btn-form" name = "submit" value = "connect">Je me connecte</button>
    </form>
</div>
</body>
</html>