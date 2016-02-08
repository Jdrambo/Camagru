<?php
session_start();
function loadClass($name){
	require("classes/".$name.".php");
}
spl_autoload_register("loadClass");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset = "utf-8">
<link rel = "stylesheet" href = "css/style.css">
</head>
<body>
<div class = "container">
	<?php include('header.php');?>
	<p>Bienvenu sur Camagru le site de retouche photo ultime</p>
</div>
</body>
</html>