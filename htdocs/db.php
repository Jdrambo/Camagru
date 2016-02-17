<?php
	if($db = new PDO('mysql:host=localhost;dbname=db_camagru', 'root', '', array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)))
	{
		$query = $db->prepare("SET NAMES 'utf8'");
		$query->execute();
	}
?>
