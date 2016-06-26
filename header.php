<header>
    <a class = "main-title" href = "index.php"><h1>Camagru</h1></a>
        <div id = "menu-list">
        <?php
        if (!isset($_SESSION['id']))
           echo '<a class = "btn-menu" href = "connexion.php">Connexion</a><a class = "btn-menu" href = "inscription.php">Inscription</a>';
        else
            echo '<a class = "btn-menu" href = "edit.php"><img class = "menu-profile-picture" alt = "Edition" title = "Edition" src = "img/cam.png">Edition</a><a class = "btn-menu" href = "profile.php"><img id = "menu-profile-picture" class = "menu-profile-picture" alt = "profile-picture-'.$_SESSION['name'].'" src = "'.$_SESSION['url'].'">Profil</a><a class = "btn-menu deco" href = "deco.php">Déconnexion</a>';
        ?>
        </div>
    <img id = "img-menu" class = "img-menu" src = "img/menu-icon.png">
</header>