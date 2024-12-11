<?php 
require ('../bdd/fonctions.php');
verifSession(); // Vérifie si une session valide existe

$estAdmin = isset($_SESSION['est_admin']) && $_SESSION['est_admin'] == 1;
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">  
        <link href="../css/navBar.css" rel="stylesheet">
        <link href="../css/accueil.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script> 
        <title>MUSEOFLOW - Acceuil</title>
    </head>
    
    <body>
        <nav class="navbar">
            <div class="logo">
                <a href="accueil.php"><img class="logo-img" src="../ressources/images/logo.png" alt="Logo MuseoFlow"></a>
                Intranet du Musée
            </div>
            <div class="main-menu">
                <?php
                    if ($estAdmin){
                        echo '<a href="utilisateurs.php" class="deco"><div class="menu-item">Utilisateurs</div></a>';
                    }
                ?>
                <a href="expositions.php" class="deco"><div class="menu-item">Expositions</div></a>
                <a href="conferenciers.php" class="deco"><div class="menu-item">Conférenciers</div></a>
                <a href="visites.php" class="deco"><div class="menu-item">Visites</div> </a>
                <a href="exportation.php" class="deco"><div class="menu-item">Exportation</div></a>
                <!-- Menu déroulant -->
                <div class="dropdown">
                    <div class="menu-item"><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($_SESSION['prenom']); ?> <i class="fa-solid fa-angle-down"></i></i></div>
                    <div class="dropdown-menu">
                        <a href="deconnexion.php" class="btn-red">Se déconnecter</a>
                    </div>
                </div>
            </div>
        </nav>

        <br><h1>Bienvenue <?php echo htmlspecialchars($_SESSION['prenom']) . ","; ?></h1><br>
            <section class="actualites-section">
                <h2>Actualités :</h2>
                <div class="news-widget">
                    <div class="news-header">
                        <!-- Barre avec les ronds pour changer de fenêtre -->
                        <div class="nav-dots">
                            <div class="dot active"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                        </div>
                        <!-- Barre horizontale -->
                        <div class="progress-bar"></div>
                    </div>
                    <div class="news-content">
                        <div class="image">
                            <img src="../ressources/images/actu1.png" alt="Actualité">
                        </div>
                        <div class="news-title">
                            <h3>Titre de l'actualité</h3>
                            <p>Description ou résumé de l'actualité ici...</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>
