 <?php
session_start();
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
        $pass = mysqli_real_escape_string($_POST['password']);
        $pass_verif = mysqli_real_escape_string($_POST['pass_verif']);
        $login = mysqli_real_escape_string($_POST['login']);
        $mail = mysqli_real_escape_string($_POST['mail']);
        $mail_verif = mysqli_real_escape_string($_POST['mail_verif']);
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
                        $query = $db->prepare('INSERT INTO account () VALUES ()');
                    }
                    else {
                        $message = "Ce login ou cette adresse e-mail sont déjà enregistré";
                    }
                }
                else {
                    $message = "Le login n'est pas valide";
                }
            }
            else
                $message = "L'adresses mail n'est pas valide";
        }
        else
            $message = "Le mot de passe n'est pas valide";
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
            <input class = "field" type = "text" name = "login" placeholder = "Identifiant">
            <input class = "field" type = "text" name = "mail" placeholder = "Adresse e-mail">
            <input class = "field" type = "text" name = "mail_verif" placeholder = "Vérification de l'adresse e-mail">
            <input class = "field" type = "password" name = "password" placeholder = "Mot de passe">
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