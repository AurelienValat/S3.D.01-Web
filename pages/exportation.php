<?php 
    session_start();
    require ('../bdd/fonctions.php');
    verifSession(); // Vérifie si une session valide existe

    $estAdmin = isset($_SESSION['est_admin']) && $_SESSION['est_admin'] == 1;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">  
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script>   
    <title>MUSEOFLOW - Exportation des fichiers</title>
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

        <div class="container content">
            <div class="container-blanc justify-content-center">
                <p>
                    Pour éviter tout conflits dans les données nous recommandons d'exporter la totalité des données en même temps.
                </p>
                <p>
                    Attention, nous vous conseillons de mettre vos fichiers une fois exportés dans un dossier prévu à cet effet.
                </p>
                <button class="btn-blue btn-action">Exporter les données</button>
            </div>
        </div>

        <br><br><br><br><br><br><br><br><br><br><br><br><br><br>

        <footer>

            <div class=”contenu-footer”>

                <div class=”bloc footer-services”>
                <h3>Nos services</h3>
                <ul class=”liste-services”>
                    <li><a href=”#”>Création de sites web</a></li>
                    <li><a href=”#”>SEO</a></li>
                    <li><a href=”#”>SEA</a></li>
                </ul>
                </div>

            <div class=”bloc footer-informations”>
                <h3>A propos</h3>
                <ul class=”liste-informations”>
                <li><a href=”#”>Actualités</a></li>
                <li><a href=”#”>Notre histoire</a></li>
                <li><a href=”#”>Investisseurs</a></li>
                <li><a href=”#”>Développement durable</a></li>
                </ul>
            </div>

            <div class=”bloc footer-contact”>
                <h3>Restons en contact</h3>
                <p>06 06 06 06 06</p>
                <p>supportclient@contact.com</p>
                <p>12 rue de l'invention, Paris, 75011</p>
            </div>

            <p class="copyright">Company Name © 2022</p>

            </div>

        </footer>
    </body>
</html>

