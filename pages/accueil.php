<?php 
require ('../bdd/fonctions.php');

verifSession(); // Vérifie si une session valide existe

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">  
        <link href="../css/navBar.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <title>MUSEOFLOW - Acceuil</title>
    </head>
    
    <body>
        <nav class="navbar">
            <div class="logo">
                <a href="accueil.php"><img class="logo-img" src="../ressources/images/logo.png" alt="Logo MuseoFlow"></a>
                Intranet du Musée
            </div>
            <div class="main-menu">
                <a href="utilisateurs.php" class="deco"><div class="menu-item">Utilisateurs</div>
                <a href="expositions.php" class="deco"><div class="menu-item">Expositions</div>
                <a href="conferenciers.php" class="deco"><div class="menu-item">Conférenciers</div>
                <a href="visites.php" class="deco"><div class="menu-item">Visites</div> </a>
                <a href="exportation.php" class="deco"><div class="menu-item">Exportation</div>
                <a href="deconnexion.php" class="deco"><div class="menu-item">Déconnexion</div></a>
            </div>
        </nav>

        <h1>Bienvenue <?php echo htmlspecialchars($_SESSION['nom']) . " " . htmlspecialchars($_SESSION['prenom']); ?></h1><br>
</html>
