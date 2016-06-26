<?php
session_start();
function loadClass($name){
	require("classes/".$name.".php");
}
spl_autoload_register("loadClass");

include("db.php");
if (isset($_POST['submit']) && $_POST['submit'] === "connect"){
    $connect = new Connect($_POST['login'], $_POST['password'], $db);
    $auth = $connect->checkUser();
    if ($auth === true){
        $sess = new SessionUser($db, $_POST['login']);
        $sess->createSession();
    }
    $message = $connect->getMessage();
}
if (!isset($_SESSION['id'])){
    ?>
    <!DOCTYPE html>
    <html>
<?php
include("head.php");
?>
    <body>
    <div class = "container">
        <?php include('header.php');?>
        <form class = "standard-form" action = "connexion.php" method = "post">
            <h2 class = "title-form">Connexion</h2>
            <?php
                if(isset($message)){

                    echo '<p class = "'.$message[1].'">'.$message[0].'</p>';
                }
            ?>
            <input class = "field" type = "text" name = "login" placeholder = "Identifiant">
            <input class = "field" type = "password" name = "password" placeholder = "Mot de passe">
            <button class = "btn-form" name = "submit" value = "connect">Je me connecte</button>
        </form>
    </div>
    <script src = "js/menu.js"></script>
    </body>
    </html>
<?php
}
else
    header("Location: index.php");