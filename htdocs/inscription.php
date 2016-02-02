<!DOCTYPE html>
<html>
<head>
<meta charset = "utf-8">
<link rel = "stylesheet" href = "css/style.css">
</head>
<body>
<div class = "container">
	<?php include('header.php');?>
    <form class = "standard-form" action = "inscription.php" method = "post">
        <h2 class = "title-form">Inscription</h2>
        <input class = "field" type = "text" name = "login" placeholder = "Identifiant">
        <input class = "field" type = "text" name = "mail" placeholder = "Adresse e-mail">
        <input class = "field" type = "text" name = "mail_verif" placeholder = "Vérification de l'adresse e-mail">
        <input class = "field" type = "password" name = "password" placeholder = "Mot de passe">
        <input class = "field" type = "password" name = "pass_verif" placeholder = "Vérification du mot de passe">
        <button class = "btn-form" name = "submit" value = "connect">Je m'inscris</button>
    </form>
</div>
</body>
</html>