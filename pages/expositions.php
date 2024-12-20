<?php
session_start();
require ('../bdd/fonctions.php');
verifSession(); // Vérifie si une session valide existe

require ('../bdd/requetes.php');
require ('../bdd/connecterBD.php');

$pdo = initierConnexion();
if ($pdo == FALSE) {
    header("Location: pages/erreurs/erreurBD.php");
}

$estAdmin = isset($_SESSION['est_admin']) && $_SESSION['est_admin'] == 1;

// Vérification si une suppression est demandée
if (isset($_POST['supprimerExposition']) && $_POST['supprimerExposition'] != trim('')) {
    $userIdToDelete = intval($_POST['supprimerExposition']); // Sécuriser la donnée
    
    try {
        supprimerLigne($pdo, $userIdToDelete, "Exposition");
    } catch (PDOException) {
        $_SESSION['donneeEnErreur'] = 'exposition';
        $_SESSION['cheminDernierePage'] = '/S3.D.01-Web/pages/expositions.php';
        header("Location: ./erreurs/impossibleDeTraiterVotreDemande.php");
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<link href="../css/style.css" rel="stylesheet">
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
    crossorigin="anonymous">
<script src="https://kit.fontawesome.com/17d5b3fa89.js"
    crossorigin="anonymous"></script>
<title>MUSEOFLOW - Gestion des Expositions</title>

</head>
<body class="fond">
    <?php require("../ressources/navBar.php");?>

    <div class="container content">

        <div class="container-blanc">
            <h1 class="text-center">Gestion des Expositions</h1>
            <div
                class="d-flex justify-content-between align-items-center">
                <button class="btn-action btn-blue" title="Ajouter une exposition"><i class="fa-solid fa-plus"></i></button>
                <button
                    class="btn btn-light d-flex align-items-center gap-2">
                    <i class="fa-solid fa-filter"></i>Filtres
                </button>
            </div>
            <div class="table">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Intitulé</th>
                            <th>Période des œuvres</th>
                            <th>Nombre d'œuvres</th>
                            <th>Mots clés</th>
                            <th>Résumé</th>
                            <th>Date de début</th>
                            <th>Date de fin</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Récupération de la liste des expositions depuis la BD
                    $expositions = getExpositions($pdo);
                    $totalExpositions = 0;

                    while ($ligne = $expositions->fetch()) {
                        echo "<tr>";
                        echo "<td>" . $ligne['intitule'] . "</td>";
                        echo "<td>" . $ligne['periode_oeuvres'] . "</td>";
                        echo "<td>" . $ligne['nombre_oeuvres'] . "</td>";
                        echo "<td>" . $ligne['mots_cles'] . "</td>";
                        echo "<td>" . $ligne['resume'] . "</td>";
                        echo "<td>" . $ligne['date_debut'] . "</td>";
                        echo "<td>" . $ligne['date_fin'] . "</td>";
                        ?>
                        <td>
                            <button class="btn-action btn-modify btn-blue" title="Modifier l'exposition"><i class="fa-solid fa-pencil"></i></button>
                            <form method="POST" action= "expositions.php" style="display:inline;">
                                <?php echo "<input type='hidden' name='supprimerExposition' value='" . $ligne['id_exposition'] . "'>";?>
                                <button type="submit" class="btn-action btn-delete" title="Supprimer l'exposition" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce conférencier ?');"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                        <?php
                        echo "</tr>";
                        echo "
                        ";
                        $totalExpositions ++;
                    }
                    ?>
                </tbody>
                </table>
            <?php
            echo $totalExpositions . " exposition(s) trouvée(s)";
            ?>
            </div>
        </div>
    </div>
</body>
</html>
