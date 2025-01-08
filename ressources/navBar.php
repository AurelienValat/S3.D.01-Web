<nav class="navbar">
    <div class="logo">
        <a href="accueil.php" title="Retour à l'accueil"><img class="logo-img"
            src="/S3.D.01-Web/ressources/images/logo.png" alt="Logo MuseoFlow"></a>
        Intranet du Musée
    </div>
    <div class="main-menu">
        <?php
            $page_courante = basename($_SERVER['PHP_SELF']); // Récupère le nom du fichier actuel
            if ($estAdmin){?>
                <a href="/S3.D.01-Web/pages/utilisateurs.php" class="deco" title="Gérer les utilisateurs">
                    <div class="menu-item <?php echo ($page_courante == "utilisateurs.php") ? "active" : ""; ?>">Utilisateurs</div>
                </a>
        <?php }
        ?>
        <a href="/S3.D.01-Web/pages/expositions.php" class="deco" title="Gérer les expositions">
            <div class="menu-item <?php echo ($page_courante == 'expositions.php') ? 'active' : ''; ?>">Expositions</div>
        </a> 
        <a href="/S3.D.01-Web/pages/conferenciers.php" class="deco" title="Gérer les conférenciers">
            <div class="menu-item <?php echo ($page_courante == 'conferenciers.php') ? 'active' : ''; ?>">Conférenciers</div>
        </a> 
        <a href="/S3.D.01-Web/pages/visites.php" class="deco" title="Gérer les vivistes">
            <div class="menu-item <?php echo ($page_courante == 'visites.php') ? 'active' : ''; ?>">Visites</div>
        </a> 
        <a href="/S3.D.01-Web/pages/exportation.php" class="deco" title="Exporter des fichiers CSV">
            <div class="menu-item <?php echo ($page_courante == 'exportation.php') ? 'active' : ''; ?>">Exportation</div>
        </a>

        <!-- Menu déroulant -->
        <div class="dropdown">
            <div class="menu-item">
                <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($_SESSION['prenom']); ?> <i
                    class="fa-solid fa-angle-down"></i>
            </div>
            <div class="dropdown-menu">
                <a href="/S3.D.01-Web/pages/deconnexion.php" class="btn-red"><span class="fa-solid fa-power-off"></span> Déconnection</a>
            </div>
        </div>
    </div>
</nav>