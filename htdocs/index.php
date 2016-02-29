<?php
session_start();
function loadClass($name){
	require("classes/".$name.".php");
}
spl_autoload_register("loadClass");
?>
<!DOCTYPE html>
<html>
<?php
include("head.php");
?>
<body>
<div class = "container">
<?php include('header.php');
if (isset($_SESSION['id']))
{
	include("db.php");
	echo '<p>Bienvenue sur Camagru le site de retouche photo ultime</p>';
	
	$query = $db->prepare('SELECT account.login, pictures.id AS picture_id, pictures.url, pictures.title, pictures.comment, DAY(pictures.date_ajout) AS day_add, MONTH(pictures.date_ajout) AS month_add,
	YEAR(pictures.date_ajout) AS year_add, HOUR(pictures.date_ajout) AS hour_add, MINUTE(pictures.date_ajout) AS min_add
    FROM pictures INNER JOIN account ON account.id = pictures.user_id WHERE pictures.published = 1
    ORDER BY pictures.date_ajout DESC LIMIT 10');
	$query->execute();
	while ($data = $query->fetch(PDO::FETCH_ASSOC)){
        $queryx = $db->prepare('SELECT `comments`.`content` AS comContent, DAY(comments.date_add) AS comDay, MONTH(comments.date_add) AS comMonth, YEAR(comments.date_add) AS comYear, HOUR(comments.date_add) AS comHour, MINUTE(comments.date_add) AS comMin, `account`.`login` AS comLogin, `icons`.`url` AS urlIcon FROM `comments` INNER JOIN `account` ON `account`.`id` = `comments`.`user_id` INNER JOIN `icons` ON `account`.`id_icon` = `icons`.`id` WHERE `comments`.`pics_id` = :pics_id ORDER BY `comments`.`date_add` DESC LIMIT 10');
        $queryx->bindValue(':pics_id', $data['picture_id']);
        $queryx->execute();
        
        $queryy = $db->prepare('SELECT COUNT(*) AS likeCount FROM `liketab` WHERE `liketab`.`pics_id` = :pics_id');
        $queryy->bindValue(':pics_id', $data['picture_id']);
        $queryy->execute();
        $count = $queryy->fetch(PDO::FETCH_ASSOC);
        
        $q = $db->prepare('SELECT * FROM `liketab` WHERE (`pics_id` = :pics_id && `user_id` = :user_id)');
        $q->bindValue(':pics_id', $data['picture_id']);
        $q->bindValue(':user_id', $_SESSION['id']);
        $q->execute();
        $like = $q->fetch(PDO::FETCH_ASSOC);
        var_dump($like);
		if ($data['day_add'] <= 9)
			$data['day_add'] = "0".$data['day_add'];
		if ($data['month_add'] <= 9)
			$data['month_add'] = "0".$data['month_add'];
        if ($data['hour_add'] <= 9)
            $data['hour_add'] = "0".$data['hour_add'];
        if ($data['min_add'] <= 9)
            $data['min_add'] = "0".$data['min_add'];
        if (isset($like['id']))
            $like_status = "Je n'aime plus";
        else
            $like_status = "J'aime";
        echo '<p><<<'.$like['id'].'>>></p>';
        echo '<h2>image '.$data['picture_id'].', session '.$_SESSION['id'].'</h2>';
		echo '<div class = "border_pics"><p><span class = "title_pics">'.$data['title'].'</span><br><span class = "login-post">'.$data['login'].'</span><br><span class = "date-post">'.$data['day_add'].'/'.$data['month_add'].'/'.$data['year_add'].', '.$data['hour_add'].'h'.$data['min_add'].'</span></p><p class = "comment_pics">'.$data['comment'].'</p><img class = "main_pics" src = "'.$data['url'].'"><p class = "command-post"><span class = "like-count" id = "like-count-'.$data['picture_id'].'">'.$count['likeCount'].'</span><img class = "img-like" src = "img/like2.png"><span class = "like-post" id = "like-post-'.$data['picture_id'].'">'.$like_status.'</span><span class = "comment-post" id = "comment-post-'.$data['picture_id'].'">Commenter</span></p><div class = "comments-block">';
        while ($com = $queryx->fetch(PDO::FETCH_ASSOC)){
            echo '<p><img class = "icon-comment" src = "'.$com['urlIcon'].'"><span class = "com-login">'.$com['comLogin'].'</span><span class = "com-text">'.$com['comContent'].'</span></p>';
        }
        echo '</div></div>';
        echo '<script src = "js/main_page.js"></script>';
	}
}
else
{
	echo '<p>Bienvenue sur Camagru le site de retouche photo ultime</p>';
}
?>
</div>
</body>
</html>