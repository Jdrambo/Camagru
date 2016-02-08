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
	<p>Bienvenue sur Camagru le site de retouche photo ultime</p>
	<?php
		if(isset($_SESSION))
			foreach ($_SESSION as $key => $value)
				echo '<p class = "field">$_SESSION[\'' . $key . '\'] => ' . $value . '</p>';
		if(isset($_SERVER))
			foreach ($_SERVER as $key => $value)
				echo '<p class = "field">$_SERVER[\'' . $key . '\'] => ' . $value . '</p>';
	?>
</div>
</body>
</html>