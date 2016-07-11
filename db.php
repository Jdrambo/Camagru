<?php
include('config/database.php');
if($db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)))
{
	$query = $db->prepare("SET NAMES 'utf8'");
	$query->execute();
}
?>
