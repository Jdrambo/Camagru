<header>
    <a href = "index.php"><h1>Camagru</h1></a>
    <?php
    if (!isset($_SESSION['id']))
	   echo '<div class = "menu-right"><a class = "btn-menu" href = "connexion.php">Connexion</a><a class = "btn-menu" href = "inscription.php">Inscription</a></div>';
    else
        echo '<div class = "menu-right"><a class = "btn-menu" href = "profile.php"><img id = "menu-profile-picture" class = "menu-profile-picture" alt = "profile-picture-'.$_SESSION['name'].'" src = "'.$_SESSION['url'].'">Profil</a><a class = "btn-menu deco" href = "deco.php">Déconnexion</a></div>';
    ?>
</header>