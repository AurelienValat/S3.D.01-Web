<?php
session_start();
require ('../bdd/fonctions.php');
verifSession(); // Vérifie si une session valide existe

if (! isset($_SESSION['est_admin']) || $_SESSION['est_admin'] != 1) {
    // Rediriger l'utilisateur vers une autre page s'il n'est pas admin
    header('Location: accueil.php');
    exit();
}

require ('../bdd/requetes.php');
require ('../bdd/connecterBD.php');
verifSession(); // Vérifie si une session valide existe

$pdo = initierConnexion();
if ($pdo == FALSE) {
    header("Location: pages/erreurs/erreurBD.php");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">  
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <title>MUSEOFLOW - Gestion des Utilisateurs</title>
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
    <div class="container-blanc">
        <h1 class="text-center">Gestion des Utilisateurs</h1>
        <div class="d-flex justify-content-between align-items-center">
            <button class="btn-action btn-modify">Ajouter un utilisateur</button>
            <button class="btn btn-light d-flex align-items-center gap-2">
            <i class="fa-solid fa-filter"></i>Filtres
            </button>
        </div>

        <div class="table">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Identifiant</th>
                        <th>Nom</th>
                        <th>Prenom</th>
                        <th>Numéro de téléphone</th>
                        <th>Administrateur</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <?php 
                    // Récupération de la liste des employés/utilisateurs depuis la BD
                    $utilisateurs = getUtilisateurs($pdo);
                    $totalUtilisateurs = 0;
                    
                    while($ligne = $utilisateurs->fetch()) {
                            echo "<tr>";
                                echo "<td>".$ligne['identifiant']."</td>";
                                echo "<td>".$ligne['nom']."</td>";
                                echo "<td>".$ligne['prenom']."</td>";
                                echo "<td>".$ligne['no_tel']."</td>";
                                echo "<td>".$ligne['est_admin']."</td>";
                                ?>
                                <td>
                                    <button class="btn-action btn-modify">Modifier</button>
                                    <button class="btn-action btn-delete">Supprimer</button>
                                </td>
                                <?php 
                            echo "</tr>";
                            echo "
                        ";
                            $totalUtilisateurs++ ;
                        }
                    ?>
                </tbody>
            </table>
            <?php echo $totalUtilisateurs . " utilisateur(s) trouvé(s)"?>
        </div>
    </div>
</div>
</body>
</html>