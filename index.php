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
    echo '<body>';
    include('header.php');
    echo '<div class = "container">';
    include("db.php");

	echo '<div id = "state_message" class = "state_message">UN MESSAGE</div>';
	echo '<p>Bienvenue sur Camagru le site de retouche photo ultime</p>';
	
    /*
    Ceci est la requête pour sélectionner les post (images) de tous les utilisateurs, si elles sont publiques
    */
    $query = $db->prepare('SELECT COUNT(id) AS count_post FROM pictures WHERE published = 1');
    $query->execute();
    if ($query->rowCount() > 0){
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $countPost = $data['count_post'];
    }
    else
        $countPost = 0;
    $limitSize = 10;
    if (isset($_GET) && isset($_GET['page']))
        $offset = ($_GET['page'] * $limitSize);
    else
        $offset = 0;
    /*
    La requête qui sélectionne tous les poste défini comme public
    Dans les limite de limitSize à partir de offset
    */
	$query = $db->prepare("SELECT account.id AS account_id, account.login, pictures.id AS picture_id, pictures.url, pictures.title, pictures.comment, DAY(pictures.date_ajout) AS day_add, MONTH(pictures.date_ajout) AS month_add,
	YEAR(pictures.date_ajout) AS year_add, HOUR(pictures.date_ajout) AS hour_add, MINUTE(pictures.date_ajout) AS min_add
    FROM pictures INNER JOIN account ON account.id = pictures.user_id WHERE pictures.published = 1
    ORDER BY pictures.date_ajout DESC LIMIT :offset, :limitSize");
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limitSize', $limitSize, PDO::PARAM_INT);
	$query->execute();
	while ($datax = $query->fetch(PDO::FETCH_ASSOC)){
        
        /*
        La requête qui sélectionne les commentaire du post actuel
        */
        $queryx = $db->prepare('SELECT `comments`.`id` AS comId, `comments`.`user_id` AS comUserId, `comments`.`content` AS comContent, DAY(comments.date_add) AS comDay, MONTH(comments.date_add) AS comMonth, YEAR(comments.date_add) AS comYear, HOUR(comments.date_add) AS comHour, MINUTE(comments.date_add) AS comMin, `account`.`login` AS comLogin, `icons`.`url` AS urlIcon FROM `comments` INNER JOIN `account` ON `account`.`id` = `comments`.`user_id` INNER JOIN `icons` ON `account`.`id_icon` = `icons`.`id` WHERE `comments`.`pics_id` = :pics_id ORDER BY `comments`.`date_add` DESC');
        $queryx->bindValue(':pics_id', $datax['picture_id']);
        $queryx->execute();
        
        /*
        Le code qui nous permettra de compte le nombre de like du post
        */
        $queryy = $db->prepare('SELECT COUNT(id) AS count_like FROM `tablk` WHERE (`tablk`.`pics_id` = :pics_id)');
        $queryy->bindValue(':pics_id', $datax['picture_id']);
        $queryy->execute();
        $likeCount = $queryy->fetch(PDO::FETCH_ASSOC);
        
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
		echo '<div class = "border_pics">
        <p class = "login-post">'.$datax['login'].'</p><p class = "date-post">'.$datax['day_add'].'/'.$datax['month_add'].'/'.$datax['year_add'].', '.$datax['hour_add'].'h'.$datax['min_add'].'</p><p class = "title_pics">'.$datax['title'].'</p><p class = "comment_pics">'.$datax['comment'].'</p><a href = "'.$datax['url'].'" target = "_blank"><img class = "main_pics" src = "'.$datax['url'].'"></a><p class = "command-post"><span class = "like-count" id = "like-count-'.$datax['picture_id'].'">'.$likeCount['count_like'].'</span><img class = "img-like" src = "img/like2.png"><span class = "like-post" id = "like-post-'.$datax['picture_id'].'">'.$like_status.'</span><span class = "comment-post" id = "comment-post-'.$id_pic.'">Commenter</span></p><div id = "general-input-border-'.$id_pic.'" class = "general-input-border"><div class = "my-comment-img-border"><img class = "my-comment-img" alt = "my_comment_profil_picture" id = "my-comment-img-'.$id_pic.'" src = "'.$_SESSION['url'].'"></div><div class = "comment-input-border"><input id = "comment-input-'.$id_pic.'" class = "comment-input" type = "text" name = "comment-input" placeholder = "Votre commentaire..."></div></div><div class = "comments-block" id = "comments-block-'.$id_pic.'">';
        $i = 0;
        // Ceci est la boucle qui affiche tous les commentaires
        while ($com = $queryx->fetch(PDO::FETCH_ASSOC)){
            if ($i % 2 > 0)
                echo '<div class = "line-comment" id = "comment-id-'.$com['comId'].'">';
            else
                echo '<div class = "line-comment2" id = "comment-id-'.$com['comId'].'">';
            
            echo '<div class = "slot-core-comment"><img class = "icon-comment" src = "'.$com['urlIcon'].'"><span class = "com-login">'.htmlspecialchars($com['comLogin']).'</span><span class = "com-text">'.htmlspecialchars($com['comContent']).'</span></div>';
            
            /*
            La condition qui définie si on affiche ou non la croix de suppression d'un commentaire
            Si le commentaire nous appartient
            Ou si on est propriétaire du post
            */
            if ($com['comUserId'] === $_SESSION['id'] || $datax['account_id'] === $_SESSION['id']){
                echo '<div class = "slot-delete-com"><img id = "delete-com-'.$com['comId'].'" title = "Supprimer le commentaire" alt = "delete comment" class = "delete-com" src = "img/delete_small.png"></div>';
            }
            echo '</div>';
            $i++;
        }
        echo '</div></div>';
	}
    
    $nbrPage = $countPost / $limitSize;
    $i = 0;
    echo '<div class = "pagination_section">';
    while ($i < $nbrPage){
        $linkClass = "pagination_slot";
        if (isset($_GET) && isset($_GET['page'])){
            if ($i === (int)$_GET['page']){
                $linkClass = "pagination_slot pagination_selected";
            }
        }
        else{
            if ($i == 0){
                $linkClass = "pagination_slot pagination_selected";
            }
        }
        echo '<a href = "index.php?page='.$i.'" class = "'.$linkClass.'">'.($i + 1).'</a>';
        $i++;
    }
    echo '</div>';
    include('footer.php');
    ?>
    </div>
    <script src = "js/main_page.js"></script>
    <script src = "js/menu.js"></script>
</body>
</html>
<?php
}
else{
    echo '<!DOCTYPE html><html>';
    include("head.php");
    echo '<body>';
    include('header.php');
    echo '<div class = "container"></div>';
    echo '<script src = "js/menu.js"></script></body></html>';
}
?>
