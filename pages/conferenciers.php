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
    if (isset($_POST['supprimerConferencier']) && $_POST['supprimerConferencier'] != trim('')) {
        $userIdToDelete = intval($_POST['supprimerConferencier']); // Sécuriser la donnée
        
        try {
            supprimerLigne($pdo, $userIdToDelete, "Conferencier");
        } catch (PDOException) {
            $_SESSION['donneeEnErreur'] = 'conférencier';
            $_SESSION['cheminDernierePage'] = '/S3.D.01-Web/pages/conferenciers.php';
            header("Location: ./erreurs/impossibleDeTraiterVotreDemande.php");
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
    <title>MUSEOFLOW - Gestion des Conférenciers</title>
</head>
<body class="fond">

    <?php require("../ressources/navBar.php");?>
    
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
                                echo '<th>Spécialité</th>';
                                echo '<th>Mots clés spécialité</th>';
                                echo '<th>Téléphone</th>';
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
                                    echo '<td>'. htmlentities($conferencier["specialite"], ENT_QUOTES) .'</td>';
                                    echo '<td>'. htmlentities($conferencier["mots_cles_specialite"], ENT_QUOTES) .'</td>';
                                    echo '<td>'. htmlentities($conferencier["no_tel"], ENT_QUOTES) .'</td>';
                                    echo '<td>';
                                        echo '<button class="btn-action btn-blue">Planning</button>';
                                        echo '<button class="btn-action btn-blue">Modifier</button>';?>
                                        <form method="POST" action= "conferenciers.php" style="display:inline;">
                                        <?php echo "<input type='hidden' name='supprimerConferencier' value='" . $conferencier['id_conferencier'] . "'>";
                                        ?> <button type="submit" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce conférencier ?');">Supprimer</button>
                                        </form>
                                        <?php 
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