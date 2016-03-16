<?php
session_start();
if(isset($_SESSION['id'])){
	include('../db.php');
    
    // Script d'enregistrement d'une image
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
		$file = $dir.hash("md5", uniqid()).".png";
		$file2 = "../".$file;
		$pics = str_replace('data:image/png;base64,', '', $pics);
		$uri = str_replace(' ', '+', $pics);
		$data = base64_decode($uri);
		
		$res = file_put_contents($file2, $data);
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
    
    // Script de suppression d'une image
    if (isset($_POST['submit']) && isset($_POST['id_pics']) && $_POST['submit'] === "delete_pics"){
        try {
            $query = $db->prepare('SELECT * FROM pictures WHERE (id = :id_pics && user_id = :user_id)');
            $query->bindValue(":id_pics", $_POST['id_pics']);
            $query->bindValue(":user_id", $_SESSION['id']);
            $query->execute();
            
            $data = $query->fetch(PDO::FETCH_ASSOC);
            if (isset($data['id'])){
                $query = $db->prepare('DELETE FROM pictures WHERE (id = :id_pics && user_id = :user_id)');
                $query->bindValue(":id_pics", $data['id']);
                $query->bindValue(":user_id", $_SESSION['id']);
                $query->execute();
                if (is_file($data['url']))
                    unlink($data['url']);
                $tab = array('true', 'Image supprimée', $data['id']);
                echo json_encode($tab);
            }
            else{
                $tab = array('false', 'Erreur : Vous n\'avez pas le droit de supprimer cette image', '');
                echo json_encode($tab);
            }
        }
        catch(Exception $e){
            $tab = array('false', 'Erreur : '.$e->getMessage());
            echo json_encode($tab);
        }
    }
    
    // Script de modification du statu publique/privée d'une image
    if (isset($_POST['submit']) && isset($_POST['id_pics']) && $_POST['submit'] === "privacy_pics"){
        $query = $db->prepare('SELECT * FROM pictures WHERE (id = :id_pics && user_id = :user_id)');
        $query->bindValue(":id_pics", $_POST['id_pics']);
        $query->bindValue(":user_id", $_SESSION['id']);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        if (isset($data['id'])){
            $id = $data['id'];
            if ($data['published'] === "0"){
                $query = $db->prepare('UPDATE pictures SET published = 1 WHERE id = :pics_id && user_id = :user_id');
                $message = 'L\'image est désormais publique';
                $privacy = 'Publique';
            }
            else {
                $query = $db->prepare('UPDATE pictures SET published = 0 WHERE id = :pics_id && user_id = :user_id');
                $message = 'L\'image est désormais privée';
                $privacy = 'Privée';
            }
            $query->bindValue(':pics_id', $data['id']);
            $query->bindValue(':user_id', $_SESSION['id']);
            $query->execute();
            $tab = array('true', $message, $id, $privacy);
            echo json_encode($tab);
        }
        else{
            $tab = array('false', 'Erreur lors de la modification du statut de l\'image');
            echo json_encode($tab);
        }
    }
    
    // Script qui enregistre un commentaire en base de données
    if (isset($_POST['submit']) && isset($_POST['content']) && isset($_POST['pics_id']) && $_POST['submit'] === "comment_post"){
        $content = trim($_POST['content']);
        if ($content !== "" && !empty($content)){
            $query = $db->prepare('INSERT INTO comments (pics_id, user_id, content, date_add) VALUES (:pics_id, :user_id, :content, NOW())');
            $query->bindValue(':pics_id', $_POST['pics_id']);
            $query->bindValue(':user_id', $_SESSION['id']);
            $query->bindValue(':content', $_POST['content']);
            $query->execute();

            $query = $db->prepare('SELECT * FROM comments WHERE pics_id = :pics_id && user_id = :user_id ORDER BY date_add DESC LIMIT 1');
            $query->bindValue(':pics_id', $_POST['pics_id']);
            $query->bindValue(':user_id', $_SESSION['id']);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);

            $tab = array('true', $_POST['pics_id'], $_POST['content'], $_SESSION['url'], $_SESSION['login'], $data['id']);
            echo json_encode($tab);
        }
        else{
            $tab = array('false', 'empty');
            echo json_encode($tab);   
        }
    }
    
    // Script de suppression d'un commentaire
    if (isset($_POST['submit']) && $_POST['submit'] === "delete_com" && isset($_POST['com_id'])){
        try{
            $query = $db->prepare('DELETE FROM comments WHERE id = :id && user_id = :user_id');
            $query->bindValue(':id', $_POST['com_id']);
            $query->bindValue(':user_id', $_SESSION['id']);
            $query->execute();
            $tab = array('true', $_POST['com_id']);
            echo json_encode($tab);
        }
        catch(Exception $e) {
            $tab = array('false', $_POST['com_id'], $e->getMessage());
            echo json_encore($tab);
        }
    }
}
else
	header("Location: index.php");
?>