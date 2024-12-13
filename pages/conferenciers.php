<?php 
    session_start();
    require ('../bdd/fonctions.php');
    require ('../bdd/connecterBD.php');
    require ('../bdd/requetes.php');
    verifSession(); // Vérifie si une session valide existe

    $estAdmin = isset($_SESSION['est_admin']) && $_SESSION['est_admin'] == 1;

    $pdo = initierConnexion();
    if ($pdo == FALSE) {
        header("Location: erreurs/erreurBD.php");
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">  
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script>  
    <title>MUSEOFLOW - Gestion des Conférenciers</title>
</head>
<body class="fond">
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
                        <div class="menu-item"><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($_SESSION['prenom']); ?> <i class="fa-solid fa-angle-down"></i></div>
                        <div class="dropdown-menu">
                            <a href="deconnexion.php" class="btn-red">Se déconnecter</a>
                        </div>
                    </div>
                </div>
            </nav>
        <div class="container content">
        <div class="container-blanc">
            <h1 class="text-center">Gestion des Conférenciers</h1>
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn-action btn-blue">Ajouter un Conférencier</button>
                <button class="btn btn-light d-flex align-items-center gap-2">
                <i class="fa-solid fa-filter"></i>Filtres
                </button>
            </div>
            <div class="table">
                <table class="table table-striped table-bordered">
                    <?php   
                        $conferenciers = afficherConferenciers($pdo);
                        $totalConferenciers = 0;
                    
                        echo '<thead class="table-dark">';
                            echo '<tr>';
                                echo '<th>Nom</th>';
                                echo '<th>Prénom</th>';
                                echo '<th>Type</th>';
                                echo '<th>Spécialités</th>';
                                echo '<th>Actions</th>';
                            echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        if (!empty($conferenciers)) {
                            foreach ($conferenciers as $conferencier) {
                                echo '<tr>';
                                    echo '<td>'. htmlentities($conferencier["nom"], ENT_QUOTES) .'</td>';
                                    echo '<td>'. htmlentities($conferencier["prenom"], ENT_QUOTES) .'</td>';
                                    echo '<td>'. (htmlentities($conferencier["est_employe_par_musee"], ENT_QUOTES) == 0 ? "Externe" : "Interne"); '</td>';
                                    echo '<td>'. htmlentities($conferencier["mots_cles_specialite"], ENT_QUOTES) .'</td>';
                                    echo '<td>';
                                        echo '<button class="btn-action btn-blue">Planning</button>';
                                        echo '<button class="btn-action btn-blue">Modifier</button>';
                                        echo '<button class="btn-action btn-delete">Supprimer</button>';
                                    echo '</td>';
                                echo '</tr>';
                                $totalConferenciers++;
                            }
                        } else {
                            echo "<tr><td colspan='5'>Aucun conférencier enregistré.</td></tr>";
                        }
                        echo '</tbody>';
                    ?>
                </table>
                <?php  echo $totalConferenciers . " conferencier(s) trouvé(s)"; ?>
            </div>
        </div>
    </div>
</body>
</html>

