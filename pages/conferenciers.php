<?php // TODO connection bd, appel de fonction
      // initialisation des variables...
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="../css/consultation.css" rel="stylesheet">
    <link href="../ressources\fontawesome-free-6.5.1-web\css\all.css" rel="stylesheet"></link>
    <title>MUSEOFLOW - Gestion des Visites</title>
</head>

<body class="fond">
    <nav class="navbar">
                <div class="logo">
                    <a href="accueil.php"><img class="logo-img" src="../ressources/images/logo.png" alt="Logo MuseoFlow"></a>
                    Intranet du Musée
                </div>
                <div class="main-menu">
                    <a href="utilisateurs.php"><div class="menu-item">Utilisateurs</div>
                    <a href="expositions.php"><div class="menu-item">Expositions</div>
                    <a href="conferenciers.php"><div class="menu-item">Conférenciers</div>
                    <a href="visites.php"><div class="menu-item">Visites</div> </a>
                    <a href="exportation.php"><div class="menu-item">Exportation</div>
                    <a href="deconnexion.php"><div class="menu-item">Déconnexion</div></a>
                </div>
            </nav>

        <div class="container content">
        <div class="container-blanc">
            <h1 class="text-center">Gestion des Visites</h1>
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn-red">Ajouter un Conférencier</button>
                <button class="btn btn-light d-flex align-items-center gap-2">
                <i class="fa-solid fa-filter"></i>Filtres
                </button>
            </div>

            <div class="table">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Type</th>
                            <th>Spécialités</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Désiré</td>
                            <td>Doué</td>
                            <td>polyvalent</td>
                            <td>dribble</td>
                            <td>
                                <button class="btn-action btn-modify">Planning</button>
                                <button class="btn-action btn-delete">Modifier</button>
                                <button class="btn-action btn-delete">Supprimer</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

