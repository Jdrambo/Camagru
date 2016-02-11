<?php
session_start();

if (isset($_SESSION['id'])){
	if (isset($_POST['submit']) && $_POST['submit'] === "select_icon"){
		include("../db.php");
		$query = $db->prepare('SELECT * FROM icons WHERE id = :id');
		$query->bindValue(":id", $_POST['id_icon']);
		$query->execute();
		$data = $query->fetch(PDO::FETCH_ASSOC);
		if (isset($data['id'])){
			$query = $db->prepare('UPDATE account SET id_icon = :id_icon WHERE id = :id_user');
			$query->bindValue(":id_icon", $_POST['id']);
			$query->bindValue(":id_user", $_SESSION['id']);
			$query->execute();
			$_SESSION['id_icon'] = $data['id'];
			$_SESSION['url'] = $data['url'];
			$_SESSION['name'] = $data['name'];
			$tab = array("true", $_SESSION['id_icon'], $_SESSION['url'], $_SESSION['name']);
			echo json_encode($tab);
		}
		else {
			$tab = array("false");
			echo json_encode($tab);
		}
	}
	else {
		$tab = array("false");
		echo json_encode($tab);
	}
}
else
	header("Location: index.php");
?>