    <nav class="navbar">
        <div class="logo">
            <a href="accueil.php"><img class="logo-img"
                src="/S3.D.01-Web/ressources/images/logo.png" alt="Logo MuseoFlow"></a>
            Intranet du Musée
        </div>
        <div class="main-menu">
               <?php
                if ($estAdmin){
                    echo '<a href="/S3.D.01-Web/pages/utilisateurs.php" class="deco"><div class="menu-item">Utilisateurs</div></a>';
                }
                ?>
                <a href="/S3.D.01-Web/pages/expositions.php" class="deco">
                    <div class="menu-item">Expositions</div>
                </a> 
                <a href="/S3.D.01-Web/pages/conferenciers.php" class="deco">
                    <div class="menu-item">Conférenciers</div>
                </a> 
                <a href="/S3.D.01-Web/pages/visites.php" class="deco">
                    <div class="menu-item">Visites</div>
                </a> 
                <a href="/S3.D.01-Web/pages/exportation.php" class="deco">
                    <div class="menu-item">Exportation</div>
                </a>
            <!-- Menu déroulant -->
            <div class="dropdown">
                <div class="menu-item">
                    <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($_SESSION['prenom']); ?> <i
                        class="fa-solid fa-angle-down"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="/S3.D.01-Web/pages/deconnexion.php" class="btn-red">Se
                        déconnecter</a>
                </div>
            </div>
        </div>
    </nav>