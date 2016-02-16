<?php
session_start();
if(isset($_SESSION['id'])){
	include('../db.php');
	if (isset($_POST['submit']) && $_POST['submit'] === "save_pics"){
		if (isset($_POST['title']))
			$title = trim($_POST['title']);
		else
			$title = "Titre";
		if (isset($_POST['comment']))
			$comment = trim($_POST['comment']);
		else
			$comment = "";
		$published = $_POST['published'];
		// Ma requete pour recuperer le dossier de l'utilisateur
		$query = $db->prepare('SELECT pictures_dir FROM account WHERE id = :id');
		$query->bindValue(":id", $_SESSION['id']);
		$query->execute();
		$data = $query->fetch(PDO::FETCH_ASSOC);
		$dir = $data['pictures_dir']."/";

		$pics = $_POST['pics'];
		$file = "../".$dir.hash("md5", uniqid()).".png";
		$pics = str_replace('data:image/png;base64,', '', $pics);
		$uri = str_replace(' ', '+', $pics);
		$data = base64_decode($uri);
		
		$res = file_put_contents($file, $data);
		if($res){
			$query = $db->prepare('INSERT INTO pictures (url, user_id, title, comment, published, date_ajout) VALUES (:url, :id, :title, :comment, :published, NOW())');
			$query->bindValue(":url", $file);
			$query->bindValue(":id", $_SESSION['id']);
			$query->bindValue(":title", $title);
			$query->bindValue(":comment", $comment);
			$query->bindValue(":published", $published);
			$query->execute();
			echo "true";
		}
		else
			echo "false";
	}
}
else
	header("Location: index.php");
?>