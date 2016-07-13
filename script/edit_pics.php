<?php
session_start();
if(isset($_SESSION['id'])){
	include('../db.php');
    
    // Script d'enregistrement d'une image
	if (isset($_POST['submit']) && $_POST['submit'] === "save_pics"){
        $tab = array('false');
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
        //On créé un nom de fichier pour l'image avec identifiant unique hashé en md5 et avec l'extension png
		$file = $dir.hash("md5", uniqid()).".png";
        //On vérifie si le fichier existe déjà auquel cas on refait un coup de uniqid hashé...
        while(file_exists("../".$file)){
            $file = $dir.hash("md5", uniqid()).".png";
        }
		$file2 = "../".$file;
		$pics = str_replace('data:image/png;base64,', '', $pics);
		$uri = str_replace(' ', '+', $pics);
		$data = base64_decode($uri);
        $temp = "../".$dir."temp.png";

       
        if ($_POST['layers'] && isset($_POST['layers']) && !empty($_POST['layers'])){
                 //$temp est un fichier temporaire que l'on réécri à chaque tour de boucle avec un nouveau calque
                file_put_contents($temp, $data);
                $layers = json_decode($_POST['layers']);
                if($layers[0]->src != ""){
                    // Ici c'est la boucle qui va fusionner tous les calque récupéré depuis le tableau d'objet nommé $layers
                    foreach ($layers as $key => $value){
                        /*
                        On créé une resource image à partir du fichier passé en paramètre de imagecreatefrompng
                        Je récupère sa taille dans un tableau [w, h] au passage
                        */
                        //$imageBase = imagecreatefrompng($temp);
                        $imageBase = imagecreatefrompng($temp);
                        $baseSize = getimagesize($temp);
                        
                        if ($layers[$key]->type === "filter"){
                            $query = $db->prepare('SELECT url FROM filters WHERE id = :id');
                            $query->bindValue(":id", $layers[$key]->pics_id);
                            $query->execute();
                            if($query->rowCount() > 0){
                                $data = $query->fetch(PDO::FETCH_ASSOC);
                                $filter = '../'.$data['url'];
                            }
                            else
                                $filter = '../img/filter/aquarel.jpg';
                        }
                        else{
                            $query = $db->prepare('SELECT url FROM emotes WHERE id = :id');
                            $query->bindValue(":id", $layers[$key]->pics_id);
                            $query->execute();
                            if($query->rowCount() > 0){
                                $data = $query->fetch(PDO::FETCH_ASSOC);
                                $filter = '../'.$data['url'];
                            }
                            else
                                $filter = '../img/emote/angel.png';
                        }
                        // CONTROLER L EXTENSION DU calque (JPG)...
                        $ext = pathinfo($layers[$key]->src, PATHINFO_EXTENSION);
                        if ($ext == "png"){
                            $imageEmote = imagecreatefrompng($filter);
                        }
                        if ($ext == "jpg" || $ext == "jpeg"){
                            $imageEmote = imagecreatefromjpeg($filter);
                        }
                        $emoteOriginalSize = getimagesize($filter);
                        
                        $mergedImage = imagecreatetruecolor($baseSize[0], $baseSize[1]);
                        $trans_color = imagecolorallocatealpha($mergedImage, 0, 0, 0, 127);
                        imagefill($mergedImage, 0, 0, $trans_color);

                        imagecopy($mergedImage, $imageBase, 0, 0, 0, 0, $baseSize[0], $baseSize[1]);

                        
                        /*
                        On fusionne les deux images, si c'est un jpg on le fusionne avec la valuer alpha passé en paramètre
                        Sinon c'est que c'est un png donc un emote on le redimensionne à la taille souhaité et on le place
                        à l'emplacement voulu
                        */
                        if($ext != "png"){
                            $resizedEmote = imagecreatetruecolor($baseSize[0], $baseSize[1]);
                            
                            //On redimensionne le filtre a la taille de notre image de base
                            imagecopyresized($resizedEmote, $imageEmote, 0, 0, 0, 0, $baseSize[0], $baseSize[1], $emoteOriginalSize[0], $emoteOriginalSize[1]);
                            imagedestroy($imageEmote);
                            $imageEmote = $resizedEmote;
                            imagecopymerge($mergedImage, $imageEmote, 0, 0, 0, 0, $layers[$key]->w, $layers[$key]->h, ($layers[$key]->alpha * 100));
                        }
                        else{
                            imagecopyresampled($mergedImage, $imageEmote, $layers[$key]->x, $layers[$key]->y, 0, 0, $layers[$key]->w, $layers[$key]->h, $emoteOriginalSize[0], $emoteOriginalSize[1]);
                        }
                        imagedestroy($imageEmote);
                        imagealphablending($mergedImage, false);
                        imagesavealpha($mergedImage, true);
                        imagepng($mergedImage, $temp);
                    }
                    //Enfin on sauvegarde le résultat
                    imagepng($mergedImage, $file2);
                    $tab = array('true', $file, $title, $layers);
                    }
                    else{
                        file_put_contents($file2, $data);
                        $tab = array('true', $file, $title);
                    }
                }
                else{
                    file_put_contents($file2, $data);
                    $tab = array('true', $file, $title);
                }
            
			$query = $db->prepare('INSERT INTO pictures (url, user_id, title, comment, published, date_ajout) VALUES (:url, :id, :title, :comment, :published, NOW())');
			$query->bindValue(":url", $file);
			$query->bindValue(":id", $_SESSION['id']);
			$query->bindValue(":title", $title);
			$query->bindValue(":comment", $comment);
			$query->bindValue(":published", $published);
			$query->execute();
		  
        echo json_encode($tab);
    
    }
    //fin du script d ajout d'une image

    
    // Script de suppression d'une image
    if (isset($_POST['submit']) && isset($_POST['id_pics']) && $_POST['submit'] === "delete_pics"){
        try {
            $query = $db->prepare('SELECT id, url, user_id, title, comment, date_ajout, published FROM pictures WHERE (id = :id_pics && user_id = :user_id)');
            $query->bindValue(":id_pics", $_POST['id_pics']);
            $query->bindValue(":user_id", $_SESSION['id']);
            $query->execute();
            
            if ($query->rowCount() > 0){
                $data = $query->fetch(PDO::FETCH_ASSOC);
                $query = $db->prepare('DELETE FROM pictures WHERE (id = :id_pics && user_id = :user_id); DELETE FROM comments WHERE pics_id = :id_pics; DELETE FROM tablk WHERE pics_id = :id_pics');
                $query->bindValue(":id_pics", $data['id']);
                $query->bindValue(":user_id", $_SESSION['id']);
                $query->execute();
                if (is_file("../".$data['url']))
                    unlink("../".$data['url']);
                $tab = array('true', 'Image supprimée', $data['id']);
                echo json_encode($tab);
            }
            else{
                $tab = array('false', 'Erreur : Vous n\'avez pas le droit de supprimer cette image');
                echo json_encode($tab);
            }
        }
        catch(Exception $e){
            $tab = array('false', 'Erreur : Veuillez contacter l\'administrateur du site');
            echo json_encode($tab);
        }
    }
    // Fin - suppression image


    // Script d'upload d'un fichier
    if(!empty($_FILES)){
        try {
            $extTab = array("image/jpg", "image/jpeg", "image/png");
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if (file_exists($_FILES['file']['tmp_name'])){
                $ext = finfo_file($finfo, $_FILES['file']['tmp_name']);

                $tab = array('false', 'Erreur lors du chargement du fichier');
                if (array_search($ext, $extTab)){
                    if((int)$_FILES['file']['size'] < 5000000){
                        $tab_exp = explode("/", $ext);
                        $ext = $tab_exp[1];

                        $query = $db->prepare('SELECT pictures_dir FROM account WHERE account.id = :user_id');
                        $query->bindValue(':user_id', $_SESSION['id']);
                        $query->execute();

                        $data = $query->fetch(PDO::FETCH_ASSOC);
                        $file_path = $data['pictures_dir']."/tempUpload.".$ext;

                        if (move_uploaded_file($_FILES['file']['tmp_name'], "../".$file_path))
                        {
                            $size = getimagesize("../".$file_path);
                            $tab = array('true', $file_path, $size);
                        }
                        else {
                            $tab = array('false', 'Erreur lors du chargement du fichier');
                        }
                    }
                    else {
                        $tab = array('false', 'La taille de votre fichier est supérieure a 5Mo et c\'est trop');
                    }
                }
            }
            else{
                $tab = array('false', 'Erreur lors du chargement du fichier');
            }
        }
        catch(Exception $e){
            $tab = array('false', 'Erreur lors du chargement du fichier');
        }
        echo json_encode($tab);
    }
    // Fin - upload fichier


    // Script de modification du statut public / privé d'une image
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
    //Fin - modification statut public

    
    // Script qui enregistre un commentaire en base de données
    if (isset($_POST['submit']) && isset($_POST['content']) && isset($_POST['pics_id']) && $_POST['submit'] === "comment_post"){
        $content = htmlspecialchars(trim($_POST['content']));
        if ($content !== "" && !empty($content)){
            /*
            La requête qui insert le commentaire en base de donnée
            */
            $query = $db->prepare('INSERT INTO comments (pics_id, user_id, content, date_add) VALUES (:pics_id, :user_id, :content, NOW())');
            $query->bindValue(':pics_id', $_POST['pics_id']);
            $query->bindValue(':user_id', $_SESSION['id']);
            $query->bindValue(':content', $content);
            $query->execute();
            
            $query = $db->prepare('SELECT account.login, account.mail FROM account INNER JOIN pictures ON pictures.user_id = account.id WHERE pictures.id = :pics_id');
            $query->bindValue(':pics_id', $_POST['pics_id']);
            $query->execute();
            if ($query->rowCount() > 0){
                $data = $query->fetch(PDO::FETCH_ASSOC);
                $mail = $data['mail'];
                /*
                Ici on envoie un mail pour avertir le propriétaire de la photo qu'il a reçu un commentaire
                */
                
                $content = '<html>
                        <head>
                            <title>Commentaire reçu sur Camagru</title>
                            <meta charset = \"utf-8\">
                            <link href="https://fonts.googleapis.com/css?family=Nothing+You+Could+Do" rel="stylesheet" type="text/css">
                            <style>
                                h1{
                                    text-align:center;
                                    font-family: \'Nothing You Could Do\', cursive;
                                    color:#448AFF;
                                }
                            </style>
                        </head>
                        <body>
                        <h1>Camagru</h1>
                        <p>Bonjour '.$data['login'].'</p>
                        <p>'.$_SESSION['login'].' a commenté votre photo sur Camagru</p>
                        <a href = "http://localhost:8080/Camagru/index.php">Camagru</a>
                        </body>
                        </html>';
                        
                    $subject = "Camagru - Commentaire";
                    $headers = "From: no-reply@camagru.fr\r\n";
                    $headers .= "Reply-To: no-reply@camagru.fr\r\n";
                    $headers .= "CC: \r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=utf-8\r\n";     
                    mail($mail, $subject, $content, $headers);
                    
            }
            
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
    //Fin - ajout commentaire

    
    // Script de suppression d'un commentaire
    if (isset($_POST['submit']) && $_POST['submit'] === "delete_com" && isset($_POST['com_id'])){
        try{
            $query = $db->prepare('DELETE comments FROM comments INNER JOIN pictures ON pictures.id = comments.pics_id WHERE comments.id = :id && (comments.user_id = :user_id || pictures.user_id = :user_id)');
            $query->bindValue(':id', $_POST['com_id']);
            $query->bindValue(':user_id', $_SESSION['id']);
            $query->execute();
            $tab = array('true', $_POST['com_id']);
            echo json_encode($tab);
        }
        catch(Exception $e) {
            $tab = array('false', $_POST['com_id'], $e->getMessage());
            echo json_encode($tab);
        }
    }
    //Fin - suppression commentaire

    
    //script de j'aime / j'aime pas d'un post
    if (isset($_POST['submit']) && $_POST['submit'] === "lkPost" && isset($_POST['pics_id'])){
        try{
            $query = $db->prepare('SELECT id FROM tablk WHERE (pics_id = :pics_id && user_id = :user_id) LIMIT 1');
            $query->bindValue(':pics_id', $_POST['pics_id']);
            $query->bindValue(':user_id', $_SESSION['id']);
            $query->execute();
            
            //Si on a déjà mis un j'aime
            if ($query->rowCount() > 0){
                $lkpost = $query->fetch(PDO::FETCH_ASSOC);
                $query = $db->prepare('DELETE FROM tablk WHERE (pics_id = :pics_id && user_id = :user_id)');
                $query->bindValue(':pics_id', $_POST['pics_id']);
                $query->bindValue(':user_id', $_SESSION['id']);
                $query->execute();
                $tab = array('true', 'removedLike', $_POST['pics_id']);
            }
            else {
                //Si on a pas déjà mis un j'aime
                $lkpost = $query->fetch(PDO::FETCH_ASSOC);
                $query = $db->prepare('INSERT INTO tablk (pics_id, user_id) VALUES (:pics_id, :user_id)');
                $query->bindValue(':pics_id', $_POST['pics_id']);
                $query->bindValue(':user_id', $_SESSION['id']);
                $query->execute();
                
            //Et on envoie un mail pour dire qu'on a mis un j'aime
            $query = $db->prepare('SELECT account.login, account.mail FROM account INNER JOIN pictures ON pictures.user_id = account.id WHERE pictures.id = :pics_id');
            $query->bindValue(':pics_id', $_POST['pics_id']);
            $query->execute();
            if ($query->rowCount() > 0){
                $data = $query->fetch(PDO::FETCH_ASSOC);
                $mail = $data['mail'];
                /*
                Ici on envoie un mail pour avertir le propriétaire de la photo qu'il a reçu un commentaire
                */
                $content = '<html>
                        <head>
                            <title>Commentaire reçu sur Camagru</title>
                            <meta charset = \"utf-8\">
                            <link href="https://fonts.googleapis.com/css?family=Nothing+You+Could+Do" rel="stylesheet" type="text/css">
                            <style>
                                h1{
                                    text-align:center;
                                    font-family: \'Nothing You Could Do\', cursive;
                                    color:#448AFF;
                                }
                            </style>
                        </head>
                        <body>
                        <h1>Camagru</h1>
                        <p>Bonjour '.$data['login'].'</p>
                        <p>'.$_SESSION['login'].' a aimé votre photo sur Camagru</p>
                        <a href = "http://localhost:8080/Camagru/index.php">Camagru</a>
                        </body>
                        </html>';
                        
                $subject = "Camagru - Commentaire";
                $headers = "From: no-reply@camagru.fr\r\n";
                $headers .= "Reply-To: no-reply@camagru.fr\r\n";
                $headers .= "CC: \r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=utf-8\r\n";     
                mail($mail, $subject, $content, $headers);
                }
                $tab = array('true', 'addedLike', $_POST['pics_id']);
            }
            echo json_encode($tab);
        }
        catch(Exception $e){
            $tab = array('false', "Une erreur s'est produite");
            echo json_encode($tab);
        }
    }
    //Fin - like / unlike
}
else
	header("Location: index.php");
?>