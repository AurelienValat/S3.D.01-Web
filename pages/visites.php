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
    <link href="../css/accueil.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script>   
    <title>MUSEOFLOW - Gestion des Visites</title>
</head>

<body class="fond">
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
    <div class="container-blanc">
        <h1 class="text-center">Gestion des Visites</h1>
        <div class="d-flex justify-content-between align-items-center">
            <button class="btn-blue">Ajouter/Réserver une visite</button>
            <button class="btn btn-light d-flex align-items-center gap-2">
            <i class="fa-solid fa-filter"></i>Filtres
            </button>
        </div>

        <div class="table">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Exposition</th>
                        <th>Conférencier</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2024-01-20</td>
                        <td>14:00</td>
                        <td>Art Moderne</td>
                        <td>Jean Dupont</td>
                        <td>
                            <button class="btn-action btn-modify">Modifier</button>
                            <button class="btn-action btn-delete">Annuler</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>

