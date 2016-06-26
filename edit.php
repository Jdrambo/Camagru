<?php
session_start();
function loadClass($name){
	require("classes/".$name.".php");
}
spl_autoload_register("loadClass");
if (isset($_SESSION['id']))
{
    //Appel a la page de connexion a la bdd
    include("db.php");
    echo '<!DOCTYPE html><html>';
    include("head.php");
    echo '<body>';
    include('header.php');
    echo '<div class = "container">';
    
    //La requete qui recupere les emotes icones
    $query = $db->prepare('SELECT id, name, url FROM emotes');
    $query->execute();
    
    //La requete qui recupere les filtres
    $queryFilters = $db->prepare('SELECT id, name, url FROM filters');
    $queryFilters->execute();
    
    //Le message d'information qui est affiché
    echo '<div id = "state_message" class = "state_message">UN MESSAGE</div>';
	echo '<p>Edition de photos</p>';

    //L'element video qui recoit le flux de la web cam
	echo '<video id="video" class = "cam-video"></video>';
    
    //Le bouton aui prend la photo
    echo '<img title = "Prendre une photo" id="startbutton" class = "cam-btn" alt = "prendre une photo" src = "img/cam.png">';
    
    //La zone d'edition de l'image
    echo '<div id = "edit-area" class = "edit-area">
            <canvas id = "canvas" class = "cam-pics"></canvas>';
    
    //La zone des emotes
    echo '<div class = "emotes-area">';
        while ($data = $query->fetch(PDO::FETCH_ASSOC)){
            echo '<div class = "emote"><img id = "emote-img-'.$data['id'].'" class = "emote-img" title = "'.$data['name'].'" src = "'.$data['url'].'"></div>';
        }
    echo '</div>';
    // La zone des filtres
    echo '<p id = "line-alpha-value">Opacité du filtre <input title = "Opacité du filtre" type = "number" id = "alpha-value" value = "0.5" min = "0" max = "1" step = "0.1"></p><div class = "filters-list">';
    while($filData = $queryFilters->fetch(PDO::FETCH_ASSOC)){
        echo '<img alt = "'.$filData['name'].'" src = "'.$filData['url'].'" title = "'.$filData['name'].'" class = "filter-pics" id = "filter-pics-'.$filData['id'].'">';
    }
    echo '</div>';
    echo '</div>
            <input id = "pics_title" type = "text" name = "title" class = "field" placeholder = "Titre de la photo...">
            <input id = "pics_comment" type = "text" name = "comment" class = "field" placeholder = "Description...">
            <p id = "container-published" class = "container-check"><input class = "form-check" name = "published" id = "pics_published" type = "checkbox">Publier</p>
            <div id = "edit-menu" class = "edit-menu">
            	<img id = "save" title = "Enregistrer la photo" class = "edit-btn" alt = "enregistrer l\'image" src = "img/save.png">
            </div></div>';
	?>
    <script src = "js/menu.js"></script>
	<script src = "js/edit.js"></script>
    </body>
</html>
<?php
}
else
	header('Location: index.php');
?>