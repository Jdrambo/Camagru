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
        $queryx = $db->prepare('SELECT comments.content AS comContent, DAY(comments.date_add) AS comDay, MONTH(comments.date_add) AS comMonth, YEAR(comments.date_add) AS comYear, HOUR(comments.date_add) AS comHour, MINUTE(comments.date_add) AS comMin, account.login AS comLogin FROM comments INNER JOIN account ON account.id = comments.user_id WHERE comments.pics_id = :pics_id ORDER BY comments.date_add DESC LIMIT 10');
        $queryx->bindValue(':pics_id', $data['picture_id']);
        $queryx->execute();
		if ($data['day_add'] <= 9)
			$data['day_add'] = "0".$data['day_add'];
		if ($data['month_add'] <= 9)
			$data['month_add'] = "0".$data['month_add'];
        if ($data['hour_add'] <= 9)
            $data['hour_add'] = "0".$data['hour_add'];
        if ($data['min_add'] <= 9)
            $data['min_add'] = "0".$data['min_add'];
		echo '<div class = "border_pics"><p class = "title_pics">'.$data['title'].'</p><p>Par : '.$data['login'].'</p><img class = "main_pics" src = "'.$data['url'].'"><p class = "comment_pics">'.$data['comment'].'</p><p>Le '.$data['day_add'].'/'.$data['month_add'].'/'.$data['year_add'].' à '.$data['hour_add'].'h'.$data['min_add'].'</p><div class = "comments-block"';
        while ($com = $queryx->fetch(PDO::FETCH_ASSOC)){
            echo '<p><span class = "com-login">'.$com['comLogin'].'</span><span class = "com-text">'.$com['comContent'].'</span></p>';
        }
        echo '</div></div>';
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