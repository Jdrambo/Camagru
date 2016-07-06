<?php
session_start();
function loadClass($name){
	require("classes/".$name.".php");
}
spl_autoload_register("loadClass");

include("db.php");

if (isset($_GET) && isset($_GET['submit']) && isset($_GET['login']) && isset($_GET['clef']) && $_GET['submit'] === "validation"){
    $query = $db->prepare('SELECT clef FROM account WHERE clef = :clef && login = :login');
    $query->bindValue(':clef', $_GET['clef']);
    $query->bindValue(':login', $_GET['login']);
    $query->execute();
    
    if ($query->rowCount() > 0){
        $query = $db->prepare('UPDATE account SET actif = 1 WHERE login = :login');
        $query->bindValue(':login', $_GET['login']);
        $query->execute();
        header('Location: connexion.php');
    }
}
/*
S'il n'existe pas de variable de session id (donc pas d'utilisateur connecté)
On affiche la page d'inscription. Sinon on renvoie l'utilisateur sur la page d'accueil.
*/
if (!isset($_SESSION['id']))
{
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
        $pattern = $data['pattern'];
        $pass_pattern = $data['pass_pattern'];
        // On vérifie que les deux mots de passes soient identiques
        if ($pass === $pass_verif && preg_match($pass_pattern, $pass)) {
            // On vérifie que les deux mail soient identiques
            if ($mail === $mail_verif && filter_var($mail, FILTER_VALIDATE_EMAIL)){
                // On vérifié que le login est valide
                $tab = ['admin', 'adm', 'administrateur', 'administrator'];
                if (preg_match($pattern, $login) && !(in_array(strtolower($pass), $tab))){
                    $query = $db->prepare('SELECT * FROM account WHERE (login COLLATE utf8_bin = :login || mail COLLATE utf8_bin = :mail)');
                    $query->bindValue(":login", $login);
                    $query->bindValue(":mail", $mail);
                    $query->execute();
                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    /*
                    Si il n'y a pas déjà un utilisateur ayant le même login ou la même adresse e-mail
                    On enregistre un nouveau compte utilisateur avec le rôle user en base de données
                    */
                    if (!(isset($data['id']))){
                        $pass = password_hash($pass, PASSWORD_DEFAULT);
                        $clef = hash("whirlpool", (microtime()*42));
                        $dir = hash("md5", (microtime().$login));
                        $dir = "img/".$dir;
                        while (is_dir($dir)){
                            $dir = hash("md5", (microtime().$login));
                            $dir = "img/".$dir;
                        }
                        mkdir($dir);
                        $query = $db->prepare('INSERT INTO account (login, mail, pass, actif, clef, type, role, id_icon, date_inscription, pictures_dir) VALUES (:login, :mail, :pass, 0, :clef, "standard", "user", 1, NOW(), :dir)');
                        $query->bindValue(":login", $login);
                        $query->bindValue(":mail", $mail);
                        $query->bindValue(":pass", $pass);
                        $query->bindValue(":clef", $clef);
                        $query->bindValue(":dir", $dir);
                        $query->execute();
                        
                        $link = 'http://localhost:8080/Camagru/inscription.php?submit=validation&login='.$login.'&clef='.$clef;

                        // Ici nous envoyons un e-mail à l'utilisateur afin qu'il puisse valider son compte
                        $content = '<html>
                        <head>
                            <meta charset = \"utf-8\">
                            <link href="https://fonts.googleapis.com/css?family=Nothing+You+Could+Do" rel="stylesheet" type="text/css">
                            <style>
                                h1{
                                    text-align:center;
                                    font-family: \'Nothing You Could Do\', cursive;
                                    color:#448AFF;
                                }
                            </style>
                        </head>
                        <body>
                        <h1>Bonjour et bienvenue sur Camagru</h1>
                        <p>Vous avez créé un compte avec l\'adresse '.$mail.' et le login '.$login.'.</p>
                        <p>Pour finaliser votre inscription, cliquez sur le lien ci dessous.</p><p><a href = "'.$link.'">Valider l\'inscription</a></p>
                        <p>Ou copiez / collez le dans la barre d\'adresse de votre navigateur.</p>
                        <p>'.$link.'</p>
                        </body>
                        </html>';
                        
                        $subject = "Camagru - Inscription";

                        $headers = "From: no-reply@camagru.fr\r\n";
                        $headers .= "Reply-To: no-reply@camagru.fr\r\n";
                        $headers .= "CC: \r\n";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
                        
                        mail($mail, $subject, $content, $headers);

                        // Le message que l'on affiche pour dire qu'on a envoyé un e-mail à l'utilisateur. (affiché en vert).
                        $message = array("Bienvenue sur Camagru ! Pour valider votre inscription un mail vous a été envoyé a l'adresse suivante : ".$mail."", "ok");
                    }
                    else
                        $message = array("Ce login ou cette adresse e-mail est déjà enregistré", "error");
                }
                else
                    $message = array("Le login n'est pas valide", "error");
            }
            else
                $message = array("L'adresses mail n'est pas valide", "error");
        }
        else
            $message = array("Le mot de passe n'est pas valide", "error");
    }
    /*
    En dessous sera le code de validation de l'inscription
    Après renvoie de l'e-mail par l'utilisateur
    */
    if (isset($_GET['submit']) && $_GET['submit'] === "validation"){
        // On vérifie que login et clef sont présent
        if (isset($_GET['login']) && isset($_GET['clef'])){
            $query = $db->prepare('SELECT * FROM `account` WHERE (`login` COLLATE utf8_bin = :login && `clef` COLLATE utf8_bin = :clef)');
            $query->bindValue(':login', $_GET['login']);
            $query->bindValue(':clef', $_GET['clef']);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            // On vérifie qu'il y a bien un utilisateur avec ce login et la clef correspondante en BDD
            if (isset($data['id'])){
                /*
                Si actif = 0 on active le compte et on passe actif a 1
                Si actif vaut 2 c'est qu'il est suspendu
                Sinon c'est quele compte est déjà actif
                */
                if (isset($data['actif']) && $data['actif'] === '0'){
                    $query = $db->prepare('UPDATE `account` SET `actif` = 1 WHERE (`login` = :login && `clef` = :clef)');
                    $query->bindValue(':login', $_GET['login']);
                    $query->bindValue(':clef', $_GET['clef']);
                    $query->execute();
                    $message = array("Votre compte a été activé avec succès", "ok");
                }
                else if (isset($data['actif']) && $data['actif'] === '2')
                    $message = array("Votre compte a été suspendu, pour plus d'informations veuillez contacter l'administrateur du site", "error");
                else
                    $message = array("Votre compte est déjà actif", "ok");
            }
            else
                $message = array("Un erreur c'est produite, veuillez contacter l'administrateur du site.", "error");
        }
        else
            $message = array("Un erreur c'est produite, veuillez contacter l'administrateur du site.", "error");
    }
    ?>
    <!DOCTYPE html>
    <html>
<?php
include("head.php");
?>
    <body>
    <?php include('header.php');?>
    <div class = "container">
        <?php
        // Si la page est chargé suite a un mail de validation on affiche pas le formulaire d'inscription
        if (isset($_GET['submit'])){
            if (isset($message))
                echo '<p class = "'.$message[1].'">'.$message[0].'</p>';
        }
        else{
        // Sinon on affiche le formulaire d'inscription
        ?>
            <form class = "standard-form" action = "inscription.php" method = "post">
                <h2 class = "title-form">Inscription</h2>
                <?php
                    if (isset($message))
                        echo '<p class = "'.$message[1].'">'.$message[0].'</p>';
                ?>
                <input class = "field" type = "text" name = "login" placeholder = "Identifiant">
                <input class = "field" type = "text" name = "mail" placeholder = "Adresse e-mail">
                <input class = "field" type = "text" name = "mail_verif" placeholder = "Vérification de l'adresse e-mail">
                <input class = "field" type = "password" name = "pass" placeholder = "Mot de passe">
                <input class = "field" type = "password" name = "pass_verif" placeholder = "Vérification du mot de passe">
                <button class = "btn-form" name = "submit" value = "inscription">Je m'inscris</button>
            </form>
        <?php
        }
        include('footer.php');
        ?>
    </div>
    <script src = "js/menu.js"></script>
    </body>
    </html>
    <?php
}
else 
    header('Location: index.php');
?>