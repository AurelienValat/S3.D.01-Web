<?php 
    require ('../bdd/fonctions.php');

    verifSession(); // Vérifie si une session valide existe
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">  
    <link href="../css/consultation.css" rel="stylesheet">
    <link href="../css/navBar.css" rel="stylesheet">
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
            <a href="utilisateurs.php" class="deco"><div class="menu-item">Utilisateurs</div></a>
            <a href="expositions.php" class="deco"><div class="menu-item">Expositions</div></a>
            <a href="conferenciers.php" class="deco"><div class="menu-item">Conférenciers</div></a>
            <a href="visites.php" class="deco"><div class="menu-item">Visites</div> </a>
            <a href="exportation.php" class="deco"><div class="menu-item">Exportation</div></a>
            <a href="deconnexion.php" class="deco"><div class="menu-item">Déconnexion</div></a>
         </div>
    </nav>
    <div class="container content">
        <div class="container-blanc justify-content-center">
            </div>
                <p>
                    Pour éviter tout conflits dans les données nous recommandons d'exporter la totalité des données en même temps.
                </p>
                <p>
                    Attention, nous vous conseillons de mettre vos fichiers une fois exportés dans un dossier prévu à cet effet.
                </p>
                <button class="btn-blue">Exporter lesdonnées</button>
            </div>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-mrcA6KGynlVHQy8MlUdJ+RbuMQVwBb0k6QZhi3EAv0eY6r60p20JztNQ2h3eG5eD" crossorigin="anonymous"></script>
</body>
</html>

