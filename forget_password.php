<?php
session_start();
function loadClass($name){
	require("classes/".$name.".php");
}
spl_autoload_register("loadClass");

include("db.php");
if (!isset($_SESSION['id'])){
    if (isset($_POST) && isset($_POST['submit']) && $_POST['submit'] === "retrieve_password"){

        // On selectionne l'adresse e-mail correspondant au login transmis
        $query = $db->prepare('SELECT mail FROM account WHERE login COLLATE utf8_bin = :login');
        $query->bindValue(':login', $_POST['login']);
        $query->execute();

        if ($query->rowCount() > 0){
            $data = $query->fetch(PDO::FETCH_ASSOC);

            // On génère une clef aléatoire qui nous permettra d'identifier l'utilisateur
            $key = hash("whirlpool", (microtime() * 42));

            // On modifie en base de donné la clé secrete correspondant a l'utilisateur
            $query = $db->prepare('UPDATE account SET clef = :clef WHERE login COLLATE utf8_bin = :login');
            $query->bindValue(':login', $_POST['login']);
            $query->bindValue(':clef', $key);
            $query->execute();

            $to = $data['mail'];
            $subject = 'Récupération de mot de passe';

            $headers = "From: no-reply@camagru.fr\r\n";
            $headers .= "Reply-To: no-reply@camagru.fr\r\n";
            $headers .= "CC: \r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=utf-8\r\n";

            $link = 'http://localhost:8080/Camagru/reset_password.php?login='.$_POST['login'].'&clef='.$key;

            $content = '<html>
            <head>
            <meta charset = "utf-8">
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
            <h1>Réinitialisation du mot de passe de Camagru</h1>
            <p>Bonjour, vous avez fais une demande de récupération de mot de passe</p>
            <p>Cliquez sur le lien suivant pour accéder a la page de récupération de mot de passe</p>
            <a href = "'.$link.'">'.$link.'</a>
            </body>';
            mail($to, $subject, $content, $headers);
            // Enfin on envoie le mail grace a la fonction mail
            $message = array("Un e-mail de réinitialisation vous a été envoyé", "ok");
        }
        else {
            $message = array("Cet identifiant n'est pas valide", "error");
        }
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
        <form class = "standard-form" action = "forget_password.php" method = "post">
            <h2 class = "title-form">Récupérer mon mot de passe</h2>
            <?php
                if(isset($message)){
                    echo '<p class = "'.$message[1].'">'.$message[0].'</p>';
                }
            ?>
            <input class = "field" type = "text" name = "login" placeholder = "Identifiant">
            <button class = "btn-form" name = "submit" value = "retrieve_password">M'envoyer un e-mail de réinitialisation</button>
        </form>
        <?php include('footer.php');?>
    </div>
    <script src = "js/menu.js"></script>
    </body>
    </html>
<?php
}
else
    header("Location: index.php");