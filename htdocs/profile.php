<?php
session_start();
function loadClass($name){
	require("classes/".$name.".php");
}
spl_autoload_register("loadClass");
if (isset($_SESSION['id'])){
    include("db.php");
    if (isset($_POST['submit']) && $_POST['submit'] === "delete_account"){
            $deleteAc = new DeleteAccount($_SESSION['id'], $_POST['pass'], $db);
            $deleteAc->eraseAccount();
        }
}
if (isset($_SESSION['id'])){
    ?>
    <!DOCTYPE html>
    <html>
    <head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "css/style.css">
    </head>
    <body>
    <div class = "container">
        <?php include('header.php');
        echo '<h2>Profile de '.$_SESSION['login'].'</h2><img class = "profile-picture" alt = "'.$_SESSION['name'].'" src = "'.$_SESSION['url'].'"><p>'.$_SESSION['mail'].'</p><p>Type de compte : '.$_SESSION['type'].'</p><p>Role : '.$_SESSION['role'].'</p>';?>
        <form class = "delete-form" action = "profile.php" method = "post">
        <p>Suppression du compte</p>
        <input class = "field" type = "password" name = "pass" placeholder = "Votre mot de passe...">
        <button class = "btn-form btn-delete" name = "submit" value = "delete_account">Supprimer mon compte</button>
        </form>
    </div>
    </body>
    </html>
<?php
}
else
    header("Location: index.php");