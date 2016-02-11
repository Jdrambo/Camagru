<?php
session_start();
function loadClass($name){
	require("classes/".$name.".php");
}
spl_autoload_register("loadClass");
?>
<!DOCTYPE html>
<html>
<?php
include("head.php");
?>
<body>
<div class = "container">
	<?php include('header.php');?>
	<p>Bienvenue sur Camagru le site de retouche photo ultime</p>
	<?php
		if(isset($_SESSION))
			foreach ($_SESSION as $key => $value)
				echo '<p class = "field">$_SESSION[\'' . $key . '\'] => ' . $value . '</p>';
	?>
</div>
</body>
</html>