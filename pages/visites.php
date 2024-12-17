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
        $utilisateurASuppr = intval($_POST['supprimerVisite']); // Sécuriser la donnée
        try {
            supprimerLigne($pdo, $utilisateurASuppr, "Visite");
        } catch (PDOException) {
            header("Location: erreurs/erreurBD.php");
        }
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
   
    <?php require("../ressources/navBar.php");?>

    <div class="container content col-12">
        <div class="container-blanc">
            <h1 class="text-center">Gestion des Visites</h1>
            <!-- Utilisation de Flexbox pour mettre sur la même ligne les boutons -->
            <div class="d-flexd-flex gap-2 mt-3">
                <!-- Menu Ajouter/Réserver -->
                    <button class="btn btn-primary btn-sm" style="cursor: pointer;">Ajouter/Réserver une visite</button>
                    
                <!-- Menu Filtres -->
                    <button class="btn btn-secondary btn-sm" style="cursor: pointer;">Filtres</button>
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
                                            echo "<button class='btn-action btn-modify btn-blue'>Modifier</button>";
                                            echo "<button class='btn-action btn-delete'>Supprimer</button>";
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const reserverDetails = document.querySelector("#reserverDetails");
        const filtreDetails = document.querySelector("#filtreDetails");

        reserverDetails.addEventListener("toggle", function () {
            if (reserverDetails.open) {
                // Cache le bouton "Filtres" si "Ajouter/Réserver" est ouvert
                filtreDetails.classList.add("hidden");
            } else {
                // Affiche le bouton "Filtres" sinon
                filtreDetails.classList.remove("hidden");
            }
        });
    });
</script>

</body>
</html>