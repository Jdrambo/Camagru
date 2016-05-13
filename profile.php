<?php
session_start();
function loadClass($name){
	require("classes/".$name.".php");
}
spl_autoload_register("loadClass");
if (isset($_SESSION['id'])){
    include("db.php");
    $icons = new IconsLib($db);
    $pics = new PicsLib($db);
    //
    if (isset($_POST['submit']) && $_POST['submit'] === "delete_account"){
        $deleteAc = new DeleteAccount($_SESSION['id'], $_POST['pass'], $db);
        $message = $deleteAc->eraseAccount();
    }
    if (isset($_POST['submit']) && $_POST['submit'] === "modif_pass"){
        $newPass = new ModifPass($db, $_SESSION['id'], $_POST['old_pass'], $_POST['new_pass'], $_POST['new_pass_verif']);
        $message = $newPass->getMessage();
    }
}
if (isset($_SESSION['id'])){
    ?>
    <!DOCTYPE html>
    <html>
<?php
include("head.php");
?>
    <body>
    <div class = "container">
        <?php include('header.php');
        //Le message pour les alert
        echo '<div id = "state_message" class = "state_message">UN MESSAGE</div>';
        //Affichage de la photo de profile et des informations du compte
        echo '<h2>Profil de '.$_SESSION['login'].'</h2><img id = "main-profile-picture" class = "profile-picture" alt = "'.$_SESSION['name'].'" src = "'.$_SESSION['url'].'"><p>'.$_SESSION['mail'].'</p><p>Type de compte : '.$_SESSION['type'].'</p><p>Inscription : Le '.$_SESSION['ins_day'].'/'.$_SESSION['ins_month'].'/'.$_SESSION['ins_year'].' à '.$_SESSION['ins_hour'].':'.$_SESSION['ins_min'].':'.$_SESSION['ins_sec'].'</p>';
        if (isset($message))
            echo '<p class = "'.$message[1].'">'.$message[0].'</p>';
        //Création de la liste de sélection d'icones
        $icons->iconSelector();
        //Création de la bibliothèques d'images
        $pics->displayLib();
        //Appel de la classe StdForm pour créer un formulaire standard
        $modif_pass = new StdForm("post", "profile.php", "standard-form regular-form", "modif_pass", "Modification du mot de passe");
        $modif_pass->addInputs(array(array("input", "password", "old_pass", "field", "Ancien mot de passe"), array("input", "password", "new_pass", "field", "Nouveau mot de passe"), array("input", "password", "new_pass_verif", "field", "Vérificaiton du mot de passe"), array("button" , "submit", "modif_pass", "btn-form", "Modifier le mot de passe")));
        ?>
        <!-- Formulaire de suppression du compte il faut rentrer le mot de passe -->
        <form class = "delete-form" action = "profile.php" method = "post">
        <h3>Suppression du compte</h3>
        <input class = "field" type = "password" name = "pass" placeholder = "Votre mot de passe...">
        <button class = "btn-form btn-delete" name = "submit" value = "delete_account">Supprimer mon compte</button>
        </form>
    </div>
    <!-- Script de selection des icones -->
    <script src = "js/select_icon.js"></script>
    <script src = "js/delete_pics.js"></script>
    </body>
    </html>
<?php
}
else
    header("Location: index.php");
?>