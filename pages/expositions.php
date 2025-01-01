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
    $expoASuppr = intval($_POST['supprimerExposition']); 
    
    try {
        supprimerLigne($pdo, $expoASuppr, "Exposition");
    } catch (PDOException) {
        $_SESSION['donneeEnErreur'] = 'exposition';
        $_SESSION['cheminDernierePage'] = '/S3.D.01-Web/pages/expositions.php';
        header("Location: ./erreurs/impossibleDeTraiterVotreDemande.php");
    }
}

//Pour la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idExposition'])) {
    try {
        $idExposition = intval($_POST['idExposition']); // Identifiant de l'expo
        $description = isset($_POST['description']) ? trim($_POST['description']) : "";
        $confirmation = isset($_POST['confirmation']) ? $_POST['confirmation'] : false; // Confirmation explicite

        // Validation des champs
        $erreursModif = [];
        if ($description == "") {
            $erreursModif['description'] = 'La description ne peut pas être vide.';
        }

        // Vérifier si l'exposition est en cours de visite
        $estEnCoursDeVisite = verifierVisitePourExpo($pdo, $idExposition);

        if (empty($erreursModif)) {
            if ($estEnCoursDeVisite && !$confirmation) {
                // Si l'exposition est en cours de visite et aucune confirmation n'a été donnée
                // TODO à mettre dans une page à part
                echo "<!DOCTYPE html>
                      <html lang='fr'>
                      <head>
                        <meta charset='utf-8'>
                      <title>Confirmation requise</title>
                      </head>
                      <body>
                          <div style='margin: 20px auto; padding: 20px; max-width: 500px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); font-family: Arial, sans-serif;'>
                            <h2 style='color: #333;'>Confirmation requise</h2>
                            <p style='color: #555; font-size: 16px;'>L'exposition est actuellement en cours de visite. Voulez-vous vraiment continuer la modification ?</p>
                            <form method='POST' action='' style='margin-top: 15px;'>
                                <input type='hidden' name='idExposition' value='" . htmlspecialchars($idExposition) . "'>
                                <input type='hidden' name='description' value='" . htmlspecialchars($description) . "'>
                                <input type='hidden' name='confirmation' value='true'>
                                <button type='submit' style='background-color: #007BFF; color: white; border: none; padding: 10px 15px; border-radius: 5px; font-size: 14px; cursor: pointer;'>Oui, continuer</button>
                                <a href='expositions.php' style='margin-left: 10px; text-decoration: none; color: #007BFF; font-size: 14px;'>Non, annuler</a>
                            </form>
                          </div>
                      </body>
                      </html>";
                exit; 
            }

            // Si l'exposition n'est pas en cours de visite ou confirmation donnée
            modifExposition($pdo, $idExposition, $description);
            echo "<script>alert('Exposition modifiée avec succès.')</script>";
        }

    } catch (Exception $e) {
        echo "<p style='color:red;'>Une erreur est survenue : " . $e->getMessage() . "</p>";
    }
}



?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script> 
    <script src="../js/expositions.js" type="text/javascript"></script>
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
                            <?php
                                echo "<button class='btn-action btn-modify btn-blue' 
                                        data-bs-toggle='modal'
                                        data-bs-target='#modalMofifierExposition' 
                                        title='Modifier l&#39;exposition'
                                        onclick='remplirFormulaire(
                                            " . intval($ligne['id_exposition']) . ", 
                                            \"" . htmlspecialchars($ligne['resume'], ENT_QUOTES) . "\"
                                        )'>
                                        <i class='fa-solid fa-pencil'></i>
                                    </button>";
                            ?>

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

        <!-- Modale Modifier Exposition -->
        <div class="modal fade <?php echo !empty($erreursModif) ? 'show' : ''; ?>" 
            id="modalMofifierExposition" 
            style="<?php echo !empty($erreursModif) ? 'display: block;' : 'display: none;'; ?>">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalModifierExpositionLabel">Modifier l'exposition</h5>
                        <a href="expositions.php" class="btn-close" aria-label="Close"></a>
                    </div>
                    <div class="modal-body">
                        <form id="formModifierExposition" method="POST" action="expositions.php">
                            <!-- Champ pour l'ID de l'exposition (caché) -->
                            <input type="hidden" id="idExposition" name="idExposition" value="<?php echo $idExposition; ?>">

                            <!-- Champ pour la description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Modifier la description</label>
                                <textarea id="description" name="description" rows="5" cols="60" 
                                        class="<?php echo isset($erreursModif['description']) ? 'is-invalid' : ''; ?>"> <?php echo htmlspecialchars($description); ?></textarea>
                                <?php if (isset($erreursModif['description'])) { ?>
                                    <div class="invalid-feedback"><?php echo $erreursModif['description']; ?></div>
                                <?php } ?>
                            </div>

                            <!-- Bouton pour soumettre le formulaire -->
                            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>