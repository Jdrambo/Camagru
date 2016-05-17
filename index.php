<?php
session_start();
function loadClass($name){
	require("classes/".$name.".php");
}
spl_autoload_register("loadClass");
if (isset($_SESSION['id']))
{
    echo '<!DOCTYPE html><html>';
    include("head.php");
    echo '<body><div class = "container">';
    include("db.php");
    include('header.php');
	
	echo '<p>Bienvenue sur Camagru le site de retouche photo ultime</p>';
	
    /*
    Ceci est la requête pour sélectionner les post (images) de tous les utilisateurs, si elles sont publiques
    J'ai limité à 10 pour afficher les autres post via du ajax (affaire à suivre)
    */
	$query = $db->prepare('SELECT account.login, pictures.id AS picture_id, pictures.url, pictures.title, pictures.comment, DAY(pictures.date_ajout) AS day_add, MONTH(pictures.date_ajout) AS month_add,
	YEAR(pictures.date_ajout) AS year_add, HOUR(pictures.date_ajout) AS hour_add, MINUTE(pictures.date_ajout) AS min_add
    FROM pictures INNER JOIN account ON account.id = pictures.user_id WHERE pictures.published = 1
    ORDER BY pictures.date_ajout DESC LIMIT 10');
	$query->execute();
	while ($datax = $query->fetch(PDO::FETCH_ASSOC)){
        $queryx = $db->prepare('SELECT `comments`.`id` AS comId, `comments`.`user_id` AS comUserId, `comments`.`content` AS comContent, DAY(comments.date_add) AS comDay, MONTH(comments.date_add) AS comMonth, YEAR(comments.date_add) AS comYear, HOUR(comments.date_add) AS comHour, MINUTE(comments.date_add) AS comMin, `account`.`login` AS comLogin, `icons`.`url` AS urlIcon FROM `comments` INNER JOIN `account` ON `account`.`id` = `comments`.`user_id` INNER JOIN `icons` ON `account`.`id_icon` = `icons`.`id` WHERE `comments`.`pics_id` = :pics_id ORDER BY `comments`.`date_add` DESC LIMIT 10');
        $queryx->bindValue(':pics_id', $datax['picture_id']);
        $queryx->execute();
        
        $queryy = $db->prepare('SELECT id FROM `tablk` WHERE (`tablk`.`pics_id` = :pics_id)');
        $queryy->bindValue(':pics_id', $datax['picture_id']);
        $queryy->execute();
        $count = $queryy->fetch(PDO::FETCH_ASSOC);
        $likeCount = count($count['id']);
        
        $id_pic = $datax['picture_id'];
        $q = $db->prepare('SELECT id, pics_id, user_id FROM `tablk` WHERE (`pics_id` = :pics_id && `user_id` = :user_id)');
        $q->bindValue(':pics_id', $datax['picture_id']);
        $q->bindValue(':user_id', $_SESSION['id']);
        $q->execute();
        $like = $q->fetch(PDO::FETCH_ASSOC);
		if ($datax['day_add'] <= 9)
			$datax['day_add'] = "0".$datax['day_add'];
		if ($datax['month_add'] <= 9)
			$datax['month_add'] = "0".$datax['month_add'];
        if ($datax['hour_add'] <= 9)
            $datax['hour_add'] = "0".$datax['hour_add'];
        if ($datax['min_add'] <= 9)
            $datax['min_add'] = "0".$datax['min_add'];
        if (isset($like['id']))
            $like_status = "Je n'aime plus";
        else
            $like_status = "J'aime";
		echo '<div class = "border_pics"><p><span class = "title_pics">'.$datax['title'].'</span><br>
        <span class = "login-post">'.$datax['login'].'</span><br><span class = "date-post">'.$datax['day_add'].'/'.$datax['month_add'].'/'.$datax['year_add'].', '.$datax['hour_add'].'h'.$datax['min_add'].'</span></p><p class = "comment_pics">'.$datax['comment'].'</p><img class = "main_pics" src = "'.$datax['url'].'"><p class = "command-post"><span class = "like-count" id = "like-count-'.$datax['picture_id'].'">'.$likeCount.'</span><img class = "img-like" src = "img/like2.png"><span class = "like-post" id = "like-post-'.$datax['picture_id'].'">'.$like_status.'</span><span class = "comment-post" id = "comment-post-'.$id_pic.'">Commenter</span></p><div id = "general-input-border-'.$id_pic.'" class = "general-input-border"><div class = "my-comment-img-border"><img class = "my-comment-img" alt = "my_comment_profil_picture" id = "my-comment-img-'.$id_pic.'" src = "'.$_SESSION['url'].'"></div><div class = "comment-input-border"><input id = "comment-input-'.$id_pic.'" class = "comment-input" type = "text" name = "comment-input" placeholder = "Votre commentaire..."></div></div><div class = "comments-block" id = "comments-block-'.$id_pic.'">';
        $i = 0;
        // Ceci est la boucle qui affiche tous les commentaires
        while ($com = $queryx->fetch(PDO::FETCH_ASSOC)){
            if ($i % 2 > 0)
                echo '<div class = "line-comment" id = "comment-id-'.$com['comId'].'">';
            else
                echo '<div class = "line-comment2" id = "comment-id-'.$com['comId'].'">';
            
            echo '<div class = "slot-core-comment"><img class = "icon-comment" src = "'.$com['urlIcon'].'"><span class = "com-login">'.htmlspecialchars($com['comLogin']).'</span><span class = "com-text">'.htmlspecialchars($com['comContent']).'</span></div>';
            if ($com['comUserId'] === $_SESSION['id']){
                echo '<div class = "slot-delete-com"><img id = "delete-com-'.$com['comId'].'" title = "Supprimer le commentaire" alt = "delete comment" class = "delete-com" src = "img/delete_small.png"></div>';
            }
            echo '</div>';
            $i++;
        }
        echo '</div></div>';
	}
    ?>
    </div>
    <script src = "js/main_page.js"></script>
</body>
</html>
<?php
}
else{
    echo '<!DOCTYPE html><html>';
    include("head.php");
    echo '<body><div class = "container">';
    include("db.php");
    include('header.php');
    echo '</body></html>';
}

?>
