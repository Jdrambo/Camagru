<?php
<<<<<<< HEAD
	session_start();
	if($db = new PDO('mysql:host=localhost;dbname=db_camagru', 'root', 'johann', array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)))
=======
	if($db = new PDO('mysql:host=localhost;dbname=db_camagru', 'root', '', array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)))
>>>>>>> 6c463eb67097e71bb94d0eb2fab0d0abf88274b6
	{
		$query = $db->prepare("SET NAMES 'utf8'");
		$query->execute();
	}
?>