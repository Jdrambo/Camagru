<?php
session_start();
function loadClass($name){
	require("classes/".$name.".php");
}

spl_autoload_register("loadClass");
    include("db.php");
    $icons = new IconsLib($db);
    $pics = new PicsLib($db);

    //La fonction qui permet d'effacer un fichier
function remove_dir($dir){
    if (isset($dir)){
        $objects = scandir($dir);
        foreach ($objects as $obj){
            if ($obj != "." && $obj != ".."){
                if(filetype($dir."/".$obj) === "dir")
                    rmdir($dir."/".$obj);
                else
                    unlink($dir."/".$obj);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

    //suppression du compte
    if (isset($_POST['submit']) && $_POST['submit'] === "delete_account"){
        
        $query = $db->prepare('SELECT id, login, mail, pass FROM account WHERE id = :id');
		$query->bindValue(':id', $_SESSION['id']);
		$query->execute();
        
        if ($query->rowCount() > 0){
		$data = $query->fetch(PDO::FETCH_ASSOC);
            if (isset($data['id']) && password_verify($_POST['pass'], $data['pass'])){
                $query = $db->prepare('SELECT `pictures`.`url`, `account`.`pictures_dir` FROM `account` LEFT JOIN `pictures` ON `pictures`.`user_id` = `account`.`id` WHERE `account`.`id` = :id');
                $query->bindValue(':id', $_SESSION['id']);
                $query->execute();

                while($data = $query->fetch(PDO::FETCH_ASSOC)){
                    if (isset($data['pictures_dir']))
                        $dir = $data['pictures_dir'];
                    if (isset($data['url']) && file_exists($data['url']))
                        unlink($data['url']);
                }
                
                if (isset($dir))
                    remove_dir($dir);

                $query = $db->prepare('DELETE `account`, `pictures`, `tablk`, `comments` FROM `account` LEFT JOIN pictures ON pictures.user_id = account.id LEFT JOIN tablk ON tablk.user_id = account.id LEFT JOIN comments ON comments.user_id = account.id WHERE `account`.`id` = :id');
                $query->bindValue(':id', $_SESSION['id']);
                $query->execute();   
                if (isset($_SESSION)){
                    foreach ($_SESSION as $key => $value){
                        unset($_SESSION[$key]);
                    }
                    session_unset($_SESSION);
                }
            }
            else
                $message = array("Vous n'avez pas saisi le bon mot de passe", "error");
        }
        else
            $message = array("Vous n'avez pas saisi le bon mot de passe", "error");
    }
    if (isset($_POST['submit']) && $_POST['submit'] === "modif_pass"){
        $newPass = new ModifPass($db, $_SESSION['id'], $_POST['old_pass'], $_POST['new_pass'], $_POST['new_pass_verif']);
        $message = $newPass->getMessage();
    }
if (isset($_SESSION['id'])){
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
        <?php include('footer.php');?>
    </div>
    <!-- Script de selection des icones -->
    <script src = "js/select_icon.js"></script>
    <script src = "js/delete_pics.js"></script>
    <script src = "js/menu.js"></script>
    </body>
    </html>
<?php
}
else
    header("Location: index.php");
?>