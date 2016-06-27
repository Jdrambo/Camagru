<?php
session_start();
include('db.php');
if (isset($_POST) && isset($_POST['submit']) && isset($_POST['pass']) && isset($_POST['pass_verif']) && isset($_POST['login']) && isset($_POST['clef']) && $_POST['submit'] === "change_pass"){

	if ($_POST['pass'] == $_POST['pass_verif']){
		
		$new_pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);

		$query = $db->prepare('UPDATE account SET pass = :pass WHERE (clef = :clef && login COLLATE utf8_bin = :login)');
		$query->bindValue(':pass', $new_pass);
		$query->bindValue(':clef', $_POST['clef']);
		$query->bindValue(':login', $_POST['login']);
		$query->execute();

        // On modifie en base de donné la clé secrete correspondant a l'utilisateur
        $key = hash("whirlpool", (microtime() * 42));
        $query = $db->prepare('UPDATE account SET clef = :clef WHERE login COLLATE utf8_bin = :login');
        $query->bindValue(':login', $_POST['login']);
        $query->bindValue(':clef', $key);
        $query->execute();
	}
	else {
		$message = array("Les mots de passe ne sont pas identique", "error");
	}
}

if(!isset($_SESSION['id']) && isset($_GET['login']) && isset($_GET['clef'])){
	$query = $db->prepare('SELECT id FROM account WHERE (clef = :clef && login COLLATE utf8_bin = :login)');
	$query->bindValue(':clef', $_GET['clef']);
	$query->bindValue(':login', $_GET['login']);
	$query->execute();
	if ($query->rowCount() > 0){
	?>
	<!DOCTYPE html>
	<html>
		<?php include('head.php');?>
		<body>
		<?php include('header.php');?>
    		<div class = "container">
			<h2>Création d'un nouveau mot de passe</h2>
			<p>Vous êtes sur le point de changer le mot de passe pour le compte de <?php echo $_GET['login'];?></p>
			<?php
                if(isset($message)){
                    echo '<p class = "'.$message[1].'">'.$message[0].'</p>';
                }
            ?>
			<form action = "reset_password.php" method = "post">
				<input pattern = ".{6,}" type = "password" name = "pass" class = "field" placeholder = "Nouveau mot de passe (au moins 6 caractères)" required>
				<input pattern = ".{6,}" type = "password" name = "pass_verif" class = "field" placeholder = "Vérification du mot de passe" required>
				<input type = "hidden" name = "clef" value = "<?php echo $_GET['clef'];?>">
				<input type = "hidden" name = "login" value = "<?php echo $_GET['login'];?>">
				<button name = "submit" value = "change_pass" class = "btn-form">Créer un nouveau mot de passe</button>
			</form>
			</div>
		</body>
	</html>
<?php
	}
	else{
		header('Location: connexion.php');
	}
}
else{
	header('Location: connexion	.php');
}
?>