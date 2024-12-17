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
    
    // Vérification si une suppression est demandée
    if (isset($_POST['supprimerVisite']) && $_POST['supprimerVisite'] != trim('')) {
        $userIdToDelete = intval($_POST['supprimerVisite']); // Sécuriser la donnée
        
        supprimerVisite($pdo, $userIdToDelete);
        
    }
    
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">  
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
    <div class="container content col-12">
    <div class="container-blanc">
        <h1 class="text-center">Gestion des Visites</h1>
        <div class="d-flex justify-content-between align-items-center">
            <button class="btn-action btn-blue">Ajouter/Réserver une visite</button>
            <button class="btn btn-light d-flex align-items-center gap-2">
            <i class="fa-solid fa-filter"></i>Filtres
            </button>
        </div>

        <div class="table">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Identifiant visite</th>
                        <th>Exposition concernée</th>
                        <th>Conférencier assurant la visite</th>
                        <th>Pris en charge par</th>
                        <th>Client ayant réservé</th>
                        <th>Tél. du client</th>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php 
                        // Récupération de la liste des employés/utilisateurs depuis la BD
                        $visites = getVisites($pdo);
                        $totalVisites = 0;
                                                
                        while($ligne = $visites->fetch()) {
                                echo "<tr>";
                                    echo "<td>".$ligne['id_visite']."</td>";
                                    echo "<td>".$ligne['intitule']."</td>";
                                    echo "<td>".$ligne['nom_conferencier']." ".$ligne['prenom_conferencier']."</td>";
                                    echo "<td>".$ligne['nom_employe']." ".$ligne['prenom_employe']."</td>";
                                    echo "<td>".$ligne['intitule_client']."</td>";
                                    echo "<td>".$ligne['no_tel_client']."</td>";
                                    echo "<td>".$ligne['date_visite']."</td>";
                                    echo "<td>".$ligne['horaire_debut']."</td>";
                                    echo "<td>";
                                        echo "<button id='btn_modifier' class='btn-action btn-modify btn-blue'>Modifier</button>";
                                        echo '<form method="POST" action= "visites.php" style="display:inline;">';
                                        echo "<input type='hidden' name='supprimerVisite' value='" . $ligne['id_visite'] . "'>";
                                        ?> <button type="submit" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?');">Supprimer</button><?php 
                                        echo "</form>";
                                    echo "</td>";
                                echo "</tr>";
                                echo "";
                                $totalVisites++ ;
                            }?>
                </tbody>
            </table>
            <?php 
            echo $totalVisites . " visites(s) trouvée(s)";
            ?>
        </div>
    </div>
</div>
</body>
</html>