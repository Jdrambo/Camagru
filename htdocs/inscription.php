<?php
session_start();
function loadClass($name){
	require($name.".php");
}
spl_autoload_register("loadClass");
/*
S'il n'existe pas de variable de session id (donc pas d'utilisateur connecté)
On affiche la page d'inscription. Sinon on renvoie l'utilisateur sur la page d'accueil.
*/
if (!isset($_SESSION['id']))
{
    include("db.php");
    // Si on valide le formulaire
    if (isset($_POST['submit']) && $_POST['submit'] === "inscription")
    {
        $query = $db->prepare('SELECT * FROM config');
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $pass = $_POST['pass'];
        $pass_verif = $_POST['pass_verif'];
        $login = $_POST['login'];
        $mail = $_POST['mail'];
        $mail_verif = $_POST['mail_verif'];
        $pref = $data['pass_prefixe'];
        $suff = $data['pass_suffixe'];
        $pattern = $data['pattern'];
        $pass_pattern = $data['pass_pattern'];
        // On vérifie que les deux mots de passes soient identiques
        if ($pass === $pass_verif && preg_match($pass_pattern, $pass)) {
            // On vérifie que les deux mail soient identiques
            if ($mail === $mail_verif && filter_var($mail, FILTER_VALIDATE_EMAIL)){
                // On vérifié que le login est valide
                $tab = ['admin', 'adm', 'administrateur', 'administrator'];
                if (preg_match($pattern, $login) && !(in_array(strtolower($pass), $tab))){
                    $query = $db->prepare('SELECT * FROM account WHERE (login = :login || mail = :mail)');
                    $query->bindValue(":login", $login);
                    $query->bindValue(":mail", $mail);
                    $query->execute();
                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    if (!(isset($data['id']))){
                        $pass = $pref.$pass.$suff;
                        $pass = hash("whirlpool", $pass);
                        $clef = hash("whirlpool", (microtime()*42));
                        $query = $db->prepare('INSERT INTO account (login, mail, pass, actif, clef, type, role, id_icone, date_inscription) VALUES (:login, :mail, :pass, 0, :clef, "standard", "user", 1, NOW())');
                        $query->bindValue(":login", $login);
                        $query->bindValue(":mail", $mail);
                        $query->bindValue(":pass", $pass);
                        $query->bindValue(":clef", $clef);
                        $query->execute();
                        $message = array("Bienvenue sur Camagru ! Pour valider votre inscription un mail vous a été envoyé a l'adresse suivante : ".$mail."", "ok");
                    }
                    else {
                        $message = array("Ce login ou cette adresse e-mail est déjà enregistré", "error");
                    }
                }
                else {
                    $message = array("Le login n'est pas valide", "error");
                }
            }
            else
                $message = array("L'adresses mail n'est pas valide", "error");
        }
        else
            $message = array("Le mot de passe n'est pas valide", "error");
    }
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
        <form class = "standard-form" action = "inscription.php" method = "post">
            <h2 class = "title-form">Inscription</h2>
            <?php
            	if (isset($message)){
            		echo '<p class = "'.$message[1].'">'.$message[0].'</p>';
            	}
            ?>
            <input class = "field" type = "text" name = "login" placeholder = "Identifiant">
            <input class = "field" type = "text" name = "mail" placeholder = "Adresse e-mail">
            <input class = "field" type = "text" name = "mail_verif" placeholder = "Vérification de l'adresse e-mail">
            <input class = "field" type = "password" name = "pass" placeholder = "Mot de passe">
            <input class = "field" type = "password" name = "pass_verif" placeholder = "Vérification du mot de passe">
            <button class = "btn-form" name = "submit" value = "inscription">Je m'inscris</button>
        </form>
    </div>
    </body>
    </html>
    <?php
}
else {
    header('Location: index.php');
}
?>