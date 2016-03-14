<?php
session_start();
if(isset($_SESSION['id'])){
	include('../db.php');
//Script de gestion des like sur un post
    if (isset($_POST['submit']) && isset($_POST['pics_id']) && $_POST['submit'] === "like_pics"){
            $qa = $db->prepare('SELECT `tablike`.`id` AS likeid , `tablike`.`status` FROM `tablike` INNER JOIN `pictures` ON `pictures`.`id` = `tablike`.`pics_id` WHERE (`tablike`.`pics_id` = :pics_id && `tablike`.`user_id` = :user_id && `pictures`.`published` = 1)');
            $qa->bindValue(":pics_id", $_POST['pics_id']);
            $qa->bindValue(":user_id", $_SESSION['id']);
            $qa->execute();

            $resp = $qa->fetch(PDO::FETCH_ASSOC);
            //echo json_encode($resp);
            if (isset($resp) && isset($resp['likeid'])){
                if ($resp['status'] === '1')
                    $qb = $db->prepare('UPDATE `tablike` SET `status` = 0 WHERE `tablike`.`id` = :id');
                if ($resp['status'] === '0')
                    $qb = $db->prepare('UPDATE `tablike` SET `status` = 1 WHERE `tablike`.`id` = :id');
                $qb->bindValue(':id', $resp['likeid']);
                $qb->execute();
                $tab = array('true', $resp['status'], $_POST['pics_id']);
                echo json_encode($tab);
            }
            else {
                $qc = $db->prepare('INSERT INTO `tablike` (`pics_id`, `user_id`, `status`) VALUES (:pics_id, :user_id, 1)');
                $qc->bindValue(':pics_id', $_POST['pics_id']);
                $qc->bindValue(':user_id', $_SESSION['id']);
                $qc->execute();
                $tab = array('true', '1', $_POST['pics_id']);
                echo json_encode($tab);
            }
    }
}
else
	header("Location: index.php");
?>