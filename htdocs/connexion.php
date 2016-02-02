<!DOCTYPE html>
<html>
<head>
<meta charset = "utf-8">
<link rel = "stylesheet" href = "css/style.css">
</head>
<body>
<div class = "container">
	<?php include('header.php');?>
	<h2>Connexion</h2>
    <form class = "standard-form" action = "connexion.php" method = "post">
        <input class = "field" type = "text" name = "login" placeholder = "Identifiant">
        <input class = "field" type = "password" name = "password" placeholder = "Mot de passe">
        <button class = "btn-form" name = "submit" value = "connect">Je me connecte</button>
    </form>
</div>
</body>
</html>