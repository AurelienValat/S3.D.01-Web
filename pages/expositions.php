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
// Initialisation des erreurs
$erreurs = [];

// Indicateur pour savoir si une visite a été créée avec succès
$expositionCree = false;

// Vérifie que la requête est de type POST et qu'elle n'est pas destinée à supprimer une visite
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['supprimerExposition']) && !isset($_POST['demandeFiltrage']) && !isset($_POST['description'])) {
    try {
        $intitule = isset($_POST['intitule']) ? trim($_POST['intitule']) : "";
        $periode_oeuvres  = isset($_POST['periode_oeuvres']) ? $_POST['periode_oeuvres'] : "";
        $nombre_oeuvres  = isset($_POST['nombre_oeuvres']) ? $_POST['nombre_oeuvres'] : "";
        $date_debut  = isset($_POST['date_debut']) ? $_POST['date_debut'] : "";
        $date_fin  = isset($_POST['date_fin']) ? $_POST['date_fin'] : "";
        $mots_cles  = isset($_POST['mots_cles']) ? $_POST['mots_cles'] : "";
        $resume  = isset($_POST['resume']) ? $_POST['resume'] : "";
        $annee_debut_oeuvres = isset($_POST['annee_debut_oeuvres']) ? trim($_POST['annee_debut_oeuvres']) : "";
        $annee_fin_oeuvres = isset($_POST['annee_fin_oeuvres']) ? trim($_POST['annee_fin_oeuvres']) : "";

        // Validation des champs supplémentaires
        // TODO vérifier les vérifs car sa sert à rien de vérifier le format si on 
        // L'impose lors du formulaire
        // Validation des champs de la période
        if ($annee_debut_oeuvres == "" || $annee_fin_oeuvres == "") {
            $erreurs['periode_oeuvres'] = "Veuillez entrer une période complète (année de début et de fin).";
        } else {
            // Vérification que les années sont des nombres
            if (!is_numeric($annee_debut_oeuvres) || !is_numeric($annee_fin_oeuvres)) {
                $erreurs['periode_oeuvres'] = "Les années doivent être des valeurs numériques.";
            } else {
                // Vérifier que l'année de début est inférieure ou égale à l'année de fin
                if ((int)$annee_debut_oeuvres > (int)$annee_fin_oeuvres) {
                    $erreurs['periode_oeuvres'] = "L'année de début doit être inférieure ou égale à l'année de fin.";
                } else {
                    // Fusionner les années dans le format souhaité
                    $periode_oeuvres = $annee_debut_oeuvres . ' - ' . $annee_fin_oeuvres;
                }
            }
        }

        // Validation du nombre d'œuvres
        if (empty($nombre_oeuvres) || $nombre_oeuvres <= 0) {
            $erreurs['nombre_oeuvres'] = "Veuillez entrer un nombre d'œuvres positif.";
        }


        if ($mots_cles == "" || count(explode(" ", $mots_cles)) > 10) {
            $erreurs['mots_cles'] = 'Veuillez entrer jusqu'."à 10 mots-clés maximum, séparés par des espaces.";
        }

        if ($resume == "" || strlen($resume) < 10 || strlen($resume) > 500) {
            $erreurs['resume'] = "Le résumé doit contenir entre 10 et 500 caractères.";
        }
       // Validation de la date de début de l'exposition
        if (empty($date_debut) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $date_debut)) {
            $erreurs['date_debut'] = "Date de début invalide ou non renseignée.";
        } else {
            // Vérification du jour (mardi à dimanche)
            $jour_semaine = date('N', strtotime($date_debut)); // 1 = lundi, 7 = dimanche
            if ($jour_semaine == 1) { // Lundi interdit
                $erreurs['date_debut'] = "Les expositions ne peuvent pas commencer un lundi.";
            } else {
                // Vérifier si la date est entre aujourd'hui et dans 3 ans
                $aujourd_hui = new DateTime();
                $date_max = (clone $aujourd_hui)->modify('+3 years');
                $date_debut_obj = DateTime::createFromFormat('Y-m-d', $date_debut);

                if (!$date_debut_obj) {
                    $erreurs['date_debut'] = "Format de date de début incorrect.";
                } elseif ($date_debut_obj < $aujourd_hui) {
                    $erreurs['date_debut'] = "La date de début doit être aujourd'hui ou dans le futur.";
                } elseif ($date_debut_obj > $date_max) {
                    $erreurs['date_debut'] = "La date de début ne peut pas dépasser 3 ans à partir d'aujourd'hui.";
                }
            }
        }

        // Validation de la date de fin de l'exposition (uniquement pour les expositions temporaires)
        if (!($date_fin == "")) {
            if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date_fin)) {
                $erreurs['date_fin'] = "Format de date de fin incorrect.";
            } else {
                $date_fin_obj = DateTime::createFromFormat('Y-m-d', $date_fin);
                $date_debut_obj = isset($date_debut_obj) ? $date_debut_obj : null;

                if (!$date_fin_obj) {
                    $erreurs['date_fin'] = "La date de fin est invalide.";
                } elseif (isset($date_debut_obj) && $date_fin_obj < $date_debut_obj) {
                    $erreurs['date_fin'] = "La date de fin doit être postérieure à la date de début.";
                }
            }
        }

        // Gestion des erreurs pour l'intitulé unique
        if ($intitule == "") {
            $erreurs['intitule'] = "L'intitulé est obligatoire.";
        } elseif (expositionExiste($pdo, $intitule)) { // Fonction pour vérifier l'unicité
            $erreurs['intitule'] = "Une exposition avec cet intitulé existe déjà.";
        }           
        
        // Si aucune erreur, créer l'exposition
        if (empty($erreurs)) {
            creerExposition($pdo, $intitule, $periode_oeuvres, $nombre_oeuvres, $mots_cles, $resume, $date_debut, $date_fin
            );
            $expositionCree = true;
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
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
        if ($description == "" || strlen($description) < 10 || strlen($description) > 500) {
            $erreursModif['description'] = 'La description ne peut pas être vide et doit contenir entre 10 et 500 caractères.';
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

    <?php 
    require("../ressources/navBar.php");
    require("../ressources/filtres.php");
    // Pour afficher les options de filtrages spécifiques aux expositions
    $_SESSION['filtreAApliquer'] = 'expositions';
    ?>

    <div class="container content">

        <div class="container-blanc">
            <h1 class="text-center">Gestion des Expositions</h1>
            <div
                class="d-flex justify-content-between align-items-center">
                <button class="btn-action btn-modify btn-blue" data-bs-toggle="modal" data-bs-target="#modalAjouterExposition" id="modalAjouterExpositionLabel" title="Ajouter une exposition"><i class="fa-solid fa-plus"></i></button>
                <button
                    class="btn btn-light d-flex align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#modalFiltrage" >
                    <i class="fa-solid fa-filter" ></i>Filtres
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
        <!-- Modal Ajouter Exposition -->
        <div class="modal fade <?php echo !empty($erreurs) ? 'show' : ''; ?>" 
            id="modalAjouterExposition" 
            style="<?php echo !empty($erreurs) ? 'display: block;' : 'display: none;'; ?>">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAjouterExpositionLabel">Ajouter une Exposition</h5>
                        <a href="expositions.php" class="btn-close" aria-label="Close"></a>
                    </div>
                    <div class="modal-body">
                        <form id="formAjouterExposition" method="POST" action="expositions.php">
                            <div class="mb-3">
                                <label for="intitule" class="form-label">Intitulé de l'exposition</label>
                                <input type="text" 
                                    id="intitule" 
                                    name="intitule" 
                                    class="form-control <?php echo isset($erreurs['intitule']) ? 'is-invalid' : ''; ?>" 
                                    value="<?php echo htmlspecialchars($intitule ?? ''); ?>"
                                    placeholder="Exposition de peinture moderne">
                                <?php if (isset($erreurs['intitule'])): ?>
                                    <div class="invalid-feedback"><?php echo $erreurs['intitule']; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="periode_oeuvres" class="form-label">Période des Œuvres</label>
                                <div class="d-flex">
                                    <input type="number" 
                                        id="annee_debut_oeuvres" 
                                        name="annee_debut_oeuvres" 
                                        class="form-control <?php echo isset($erreurs['periode_oeuvres']) ? 'is-invalid' : ''; ?>" 
                                        value="<?php echo htmlspecialchars($annee_debut_oeuvres ?? ''); ?>"
                                        placeholder="Année de début">
                                    <span class="mx-2">à</span>
                                    <input type="number" 
                                        id="annee_fin_oeuvres" 
                                        name="annee_fin_oeuvres" 
                                        class="form-control <?php echo isset($erreurs['periode_oeuvres']) ? 'is-invalid' : ''; ?>" 
                                        value="<?php echo htmlspecialchars($annee_fin_oeuvres ?? ''); ?>"
                                        placeholder="Année de fin">
                                </div>
                                <?php if (isset($erreurs['periode_oeuvres'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?php echo $erreurs['periode_oeuvres']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="nombre_oeuvres" class="form-label">Nombre d'Œuvres</label>
                                <input type="number" 
                                    id="nombre_oeuvres" 
                                    name="nombre_oeuvres" 
                                    class="form-control <?php echo isset($erreurs['nombre_oeuvres']) ? 'is-invalid' : ''; ?>" 
                                    value="<?php echo htmlspecialchars($nombre_oeuvres ?? ''); ?>"
                                    placeholder="Ex. 10">
                                <?php if (isset($erreurs['nombre_oeuvres'])): ?>
                                    <div class="invalid-feedback"><?php echo $erreurs['nombre_oeuvres']; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="date_debut" class="form-label">Date de début</label>
                                <input type="date" 
                                    id="date_debut" 
                                    name="date_debut" 
                                    class="form-control <?php echo isset($erreurs['date_debut']) ? 'is-invalid' : ''; ?>" 
                                    value="<?php echo htmlspecialchars($date_debut ?? ''); ?>"
                                    placeholder="YYYY-MM-DD">
                                <?php if (isset($erreurs['date_debut'])): ?>
                                    <div class="invalid-feedback"><?php echo $erreurs['date_debut']; ?></div>
                                <?php endif; ?>
                            </div>
                            <!-- Faire en sorte de pouvoir supprimer la date de fin si l'utilisateur c'est trompé et ne veut pas la mettre -->
                            <div class="mb-3">
                                <label for="date_fin" class="form-label">Date de fin (optionnelle)</label>
                                <input type="date" 
                                    id="date_fin" 
                                    name="date_fin" 
                                    class="form-control <?php echo isset($erreurs['date_fin']) ? 'is-invalid' : ''; ?>" 
                                    value="<?php echo htmlspecialchars($date_fin ?? ''); ?>"
                                    placeholder="YYYY-MM-DD (optionnelle)">
                                <?php if (isset($erreurs['date_fin'])): ?>
                                    <div class="invalid-feedback"><?php echo $erreurs['date_fin']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="mots_cles" class="form-label">Mots-clés</label>
                                <input type="text" 
                                    id="mots_cles" 
                                    name="mots_cles" 
                                    class="form-control <?php echo isset($erreurs['mots_cles']) ? 'is-invalid' : ''; ?>" 
                                    value="<?php echo htmlspecialchars($mots_cles ?? ''); ?>"
                                    placeholder="Ex. Art, Peinture, Contemporain">
                                <?php if (isset($erreurs['mots_cles'])): ?>
                                    <div class="invalid-feedback"><?php echo $erreurs['mots_cles']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="resume" class="form-label">Résumé de l'exposition</label>
                                <textarea id="resume" 
                                        name="resume" 
                                        class="form-control <?php echo isset($erreurs['resume']) ? 'is-invalid' : ''; ?>"
                                        rows="3"
                                        placeholder="Description de l'exposition"><?php echo htmlspecialchars($resume ?? ''); ?></textarea>
                                <?php if (isset($erreurs['resume'])): ?>
                                    <div class="invalid-feedback"><?php echo $erreurs['resume']; ?></div>
                                <?php endif; ?>
                            </div>
                                    <button type="submit" class="btn btn-primary">Créer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modale de Confirmation -->
                <div class="modal <?php echo $expositionCree ? 'show' : ''; ?>" 
                    id="modalConfirmation" 
                    tabindex="-1" 
                    aria-labelledby="modalConfirmationLabel" 
                    aria-hidden="<?php echo $expositionCree ? 'false' : 'true'; ?>" 
                    style="<?php echo $expositionCree ? 'display: block;' : 'display: none;'; ?>">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalConfirmationLabel">Succès</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Visite créé avec succès.</p>
                            </div>
                            <div class="modal-footer">
                                <a href="expositions.php" class="btn btn-secondary">Fermer</a>
                            </div>
                        </div>
                    </div>
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
    <?php require("../ressources/footer.php");?>
</body>
</html>