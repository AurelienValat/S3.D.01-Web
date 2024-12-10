<?php // TODO connection bd, appel de fonction
      // initialisation des variables...
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="../css/visites.css" rel="stylesheet">
    <title>MUSEOFLOW - Gestion des Visites</title>
</head>

<body class="fond">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="logo">
                <a href="..\index.php"><img class="logo-img" src="../ressources/images/logo.png" alt="Logo MuseoFlow"></a>
                <div class="policeBlanche"> Intranet du Musée </div>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Utilisateurs</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Expositions</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Conférenciers</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Visites</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Exportation</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container content">
    <div class="container-blanc">
        <h1 class="text-center">Gestion des Visites</h1>
        <div class="d-flex justify-content-between align-items-center">
            <button class="btn-red">Ajouter/Réserver une visite</button>
            <button class="btn btn-light d-flex align-items-center gap-2">
                <img src="../ressources/images/filtre-icon.png" alt="Filtres" width="20">
                Filtres
            </button>
        </div>

        <div class="table">
            <table class="table table-striped table-bordered">
                <thead class="table-striped">
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-mrcA6KGynlVHQy8MlUdJ+RbuMQVwBb0k6QZhi3EAv0eY6r60p20JztNQ2h3eG5eD" crossorigin="anonymous"></script>
</body>
</html>

