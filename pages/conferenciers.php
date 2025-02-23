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
    
    // Initialisation des erreurs
    $erreurs = [];

    $conferenciersCree = false;
    // On vérifie que l'on est pas en train de réaliser un autre traitement
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['supprimerConferencier']) && !isset($_POST['idConferencier']) && !isset($_POST['demandeFiltrage'])) {
        try {
            // Initialisation des variables de formulaire
            $type = isset($_POST['type']) ? trim($_POST['type']) : "";
            $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : "";
            $nom = isset($_POST['nom']) ? trim($_POST['nom']) : "";
            $specialite = isset($_POST['specialite']) ? trim($_POST['specialite']) : "";
            $motSpecialite = isset($_POST['motSpecialite']) ? trim($_POST['motSpecialite']) : "";
            $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : "";
            // $indisponibilite_debut = isset($_POST['indisponibilite_debut']) ? trim($_POST['indisponibilite_debut']) : "";
            // $indisponibilite_fin = isset($_POST['indisponibilite_fin']) ? trim($_POST['indisponibilite_fin']) : "";

            // Validation des champs
            if ($motSpecialite == "" || count(explode(" ", $motSpecialite)) > 6) {
                $erreurs['motSpecialite'] = 'La spécialité doit contenir entre 1 et 6 mots-clés séparés par des espaces.';
            }
            if (str_contains($motSpecialite, ';')) {
                $erreurs['motSpecialite'] = 'Le caractère \';\' est interdit.';
            }
            
            if ($type == "") {
                $erreurs['type'] = 'Le type est requis.';
            }
            if (str_contains($type, ';')) {
                $erreurs['type'] = 'Le caractère \';\' est interdit.';
            }
            
            if (($prenom == "") || strlen($prenom) > 50) {
                $erreurs['prenom'] = 'Le prénom est requis et ne doit pas dépasser 50 caractères.';
            }
            if (str_contains($prenom, ';')) {
                $erreurs['prenom'] = 'Le caractère \';\' est interdit.';
            }
            
            if (($nom == "") || strlen($nom) > 50) {
                $erreurs['nom'] = 'Le nom est requis et ne doit pas dépasser 50 caractères.';
            }
            if (str_contains($nom, ';')) {
                $erreurs['nom'] = 'Le caractère \';\' est interdit.';
            }
            
            if (($specialite == "") || strlen($specialite) > 50) {
                $erreurs['specialite'] = 'La specialite est requise et ne doit pas dépasser 50 caractères.';
            }
            if (str_contains($specialite, ';')) {
                $erreurs['specialite'] = 'Le caractère \';\' est interdit.';
            }
            
            if (!preg_match("#^[0-9]{10}#", $telephone) or strlen($telephone)>10 ) {
                $erreurs['telephone'] = 'Numéro de téléphone invalide. Il doit contenir 10 chiffres.';
            }
            if (str_contains($telephone, ';')) {
                $erreurs['telephone'] = 'Le caractère \';\' est interdit.';
            }
            
            // Si aucun champ n'a d'erreur, procéder à l'insertion
            if (empty($erreurs)) {
                if (verifierExistanceConferencier($pdo, $nom, $prenom)) {
                    $erreurs['existance'] = 'Un conférencier avec ce nom et prénom existe déjà.';
                } else {
                    creerConferencier($pdo, $nom, $prenom, $type, $specialite, $motSpecialite, $telephone);
                    $conferenciersCree = true; // Indique qu'un utilisateur a été créé
                }
            }
            
        } catch (Exception $e) {
            echo "<p style='color:red;'>".$e->getMessage()."</p>";
        }
    }  else {
        // Initialiser les variables de formulaire à des valeurs vides si pas de soumission
        $motSpecialite = $type = $telephone = $prenom = $nom = $specialite = "";
    }

    //Pour la modification
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idConferencier'])) {
        // Si c'est une modification du conférencier
        if (isset($_POST['action'])) {
            try {
                $idConferencier = intval($_POST['idConferencier']);
                $prenomModif = isset($_POST['prenomConferencier']) ? trim($_POST['prenomConferencier']) : "";
                $nomModif = isset($_POST['nomConferencier']) ? trim($_POST['nomConferencier']) : "";
                $telephoneModif = isset($_POST['telephoneConferencier']) ? trim($_POST['telephoneConferencier']) : "";
                $motSpecialiteModif = isset($_POST['motsCleSpe']) ? trim($_POST['motsCleSpe']) : "";

                // Validation des champs
                $erreursModif = [];
                if (($prenomModif == "") || strlen($prenomModif) > 35) {
                    $erreursModif['prenom'] = 'Le prénom est requis et ne doit pas dépasser 35 caractères.';
                }
                if (str_contains($prenomModif, ';')) {
                    $erreursModif['prenom'] = 'Le caractère \';\' est interdit.';
                }
                
                if (($nomModif == "") || strlen($nomModif) > 35) {
                    $erreursModif['nom'] = 'Le nom est requis et ne doit pas dépasser 35 caractères.';
                }
                if (str_contains($nomModif, ';')) {
                    $erreursModif['nom'] = 'Le caractère \';\' est interdit.';
                }
                
                if (!preg_match("/^[0-9]{10}$/", $telephoneModif)) {
                    $erreursModif['telephone'] = 'Numéro de téléphone invalide. Il doit contenir 10 chiffres.';
                }
                if (str_contains($telephoneModif, ';')) {
                    $erreursModif['telephone'] = 'Le caractère \';\' est interdit.';
                }
                
                if (($motSpecialiteModif == "") || count(explode(" ", $motSpecialiteModif)) > 6){
                    $erreursModif['motsCleSpecialite'] = 'La spécialité doit contenir entre 1 et 6 mots-clés séparés par des espaces.';
                }
                if (str_contains($motSpecialiteModif, ';')) {
                    $erreursModif['motsCleSpecialite'] = 'Le caractère \';\' est interdit.';
                }

                // Si aucune erreur, mise à jour
                if (empty($erreursModif) && $_POST['action'] === 'modifierConferencier') {
                    if (verifierExistanceConferencierModif($pdo, $nomModif, $prenomModif, $idConferencier)) {
                        $erreursModif['existance'] = 'Un conférencier avec ce nom et prénom existe déjà.';
                    } else {
                        // Mise à jour du conférencier dans la base
                        modifConferencier($pdo, $idConferencier, [
                            'prenom' => $prenomModif,
                            'nom' => $nomModif,
                            'no_tel' => $telephoneModif,
                            'mots_cles_specialite' => $motSpecialiteModif,
                        ]);

                        // Affichage du message de succès
                        header("Location: conferenciers.php?message=" . urlencode("Conférencier modifié avec succès."));

                    }
                }
            } catch (Exception $e) {
                echo "<p style='color:red;'>Une erreur est survenue : " . $e->getMessage() . "</p>";
            }
        }   
    }
    
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="icon" type="image/png" href="../ressources/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="../ressources/favicon/favicon.svg" />
    <link rel="shortcut icon" href="../ressources/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="../ressources/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MuseoFlow" />
    <meta charset="utf-8">  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="../css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script>
    <script src="../js/conferenciers.js" type="text/javascript"></script>
    <title>MUSEOFLOW - Gestion des Conférenciers</title>
</head>
<body>

    <?php 
    // Pour afficher les options de filtrages spécifiques aux conférenciers
    $_SESSION['filtreAApliquer'] = 'conférenciers';
    require("../ressources/navBar.php");
    require("../ressources/filtres.php");
    ?>
    
    <div class="container content">
        <div class="container-blanc">
            <?php
            if (isset($_GET['message']) && $_GET['message'] === "Conférencier modifié avec succès.") {
                echo "<script>alert('" . addslashes($_GET['message']) . "');</script>";
            }
            ?>
            <h1 class="text-center">Gestion des Conférenciers</h1>
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn-action btn-modify btn-blue" data-bs-toggle="modal" data-bs-target="#modalAjouterConferencier" id="modalAjouterConferencierLabel" title="Ajouter un conférencier"><i class="fa-solid fa-user-plus"></i></button>
                <button
                    class="btn btn-secondary d-flex align-items-center gap-2 filtrage"
                    data-bs-toggle="modal" data-bs-target="#modalFiltrage" >
                    <i class="fa-solid fa-filter" ></i>Filtres
                </button>
            </div>
            <div class="table">
                <table class="table table-striped table-bordered">
                <?php   
                    
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['demandeFiltrage']) && $_POST['demandeFiltrage'] === '1') {
                        $nomRecherche = $_POST['rechercheNom'] ?? '';
                        $prenomRecherche = $_POST['recherchePrenom'] ?? '';
                        $typeRecherche = $_POST['rechercheType'] ?? '';
                        $specialiteRecherche = $_POST['rechercheSpecialite'] ?? '';
                        $motsClesRecherche = $_POST['rechercheMotsCles'] ?? '';

                        $conferenciers = rechercheConferenciers($pdo, $nomRecherche, $prenomRecherche, $typeRecherche, $specialiteRecherche, $motsClesRecherche);
                       
                        echo '<a href="conferenciers.php"><button class="btn-action btn-modify btn-blue"><span class="fa fa-refresh"></span> Effacer les filtres</button></a><br>';
                        echo '<h5>Filtres appliqués :</h5>';
                        
                        // Affichage des filtres appliqués
                        if (!empty($_POST['rechercheNom'])) {
                            echo "Nom : '" . htmlspecialchars($_POST['rechercheNom']) . "' :<br>";
                        }
                        if (!empty($_POST['recherchePrenom'])) {
                            echo "Prénom : '" . htmlspecialchars($_POST['recherchePrenom']) . "' :<br>";
                        }
                        // Pas de empty car valeur 0 possible
                        if (isset($_POST['rechercheType']) && $_POST['rechercheType'] != '') {
                            if ($_POST['rechercheType'] == 0) {
                                echo 'Type : Externe <br>';
                            } else {
                                echo 'Type : Interne <br>';
                            }
                        }
                        if (!empty($_POST['rechercheSpecialite'])) {
                            echo "Spécialité : '" . htmlspecialchars($_POST['rechercheSpecialite']) . "' :<br>";
                        }
                        if (!empty($_POST['rechercheMotsCles'])) {
                            echo "Mots Clés : '" . htmlspecialchars($_POST['rechercheMotsCles']) . "' :<br>";
                        }
                        echo '<hr>';
                        
                    } else {
                        $conferenciers = getConferenciers($pdo);
                    }
                    
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
                        while ($ligne = $conferenciers->fetch()) {
                            echo '<tr>';
                                echo '<td>' . htmlspecialchars($ligne["nom"], ENT_QUOTES) . '</td>';
                                echo '<td>' . htmlspecialchars($ligne["prenom"], ENT_QUOTES) . '</td>';
                                echo '<td>' . (intval($ligne["est_employe_par_musee"]) === 0 ? "Externe" : "Interne") . '</td>';
                                echo '<td>' . htmlspecialchars($ligne["specialite"], ENT_QUOTES) . '</td>';
                                echo '<td>' . htmlspecialchars($ligne["mots_cles_specialite"], ENT_QUOTES) . '</td>';
                                echo '<td>' . htmlspecialchars($ligne["no_tel"], ENT_QUOTES) . '</td>';
                                echo '<td>';

                                    ?>
                                    <form method="post" action="planning.php">
                                        <input type="hidden" name="idConferencier" value="<?php if(isset($ligne["id_conferencier"])) {echo $ligne["id_conferencier"];} ?>">
                                        <?php $_SESSION['idConferencier'] = $ligne["id_conferencier"];?>
                                        <button type="submit" class="btn-action btn-blue" title="Afficher le planning du conférencier">
                                        <i class="fa-solid fa-calendar"></i>
                                        </button>
                                    </form>

                                    <?php


                                    
                                    echo '<button class="btn-action btn-modify btn-blue" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalMofifierConferencier" 
                                            id="modalModifierConferencierLabel"
                                            title="Modifier le conférencier"
                                            onclick="remplirFormulaire(
                                                ' . intval($ligne["id_conferencier"]) . ', 
                                                \'' . addslashes($ligne["prenom"]) . '\',
                                                \'' . addslashes($ligne["nom"]) . '\',
                                                \'' . addslashes($ligne["no_tel"]) . '\',
                                                \'' . addslashes($ligne["mots_cles_specialite"]) . '\'
                                            )">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>';
                                    echo '<form method="POST" action="conferenciers.php" style="display:inline;">';
                                        echo '<input type="hidden" name="supprimerConferencier" value="' . intval($ligne['id_conferencier']) . '">';
                                        echo '<button type="submit" class="btn-action btn-delete" 
                                                onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce conférencier ?\');" 
                                                title="Supprimer le conférencier">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>';
                                    echo '</form>';
                                echo '</td>';
                            echo '</tr>';
                            $totalConferenciers++;
                        }
                    } else {
                        echo "<tr><td colspan='7'>Aucun conférencier enregistré.</td></tr>";
                    }
                    echo '</tbody>';
                    ?>

                </table>
                <?php  echo $totalConferenciers . " conferencier(s) trouvé(s)"; ?>
            </div>
        </div>
    </div>
    
   <!-- Modale Ajouter Conferencier -->
    <div class="modal fade <?php echo !empty($erreurs) ? 'show' : ''; ?>" 
        id="modalAjouterConferencier" 
        style="<?php echo !empty($erreurs) ? 'display: block;' : 'display: none;'; ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAjouterConferencierLabel">Ajouter un Conférencier</h5>
                    <a href="conferenciers.php" class="btn-close" aria-label="Close"></a>
                </div>
                <div class="modal-body">
                    <form id="formAjouterConferencier" method="POST" action="conferenciers.php">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" 
                                class="form-control <?php echo isset($erreurs['nom']) ? 'is-invalid' : ''; ?>" 
                                id="nom" 
                                name="nom" 
                                value="<?php echo htmlspecialchars($nom); ?>" 
                                placeholder="Entrez le nom">
                            <?php if (isset($erreurs['nom'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['nom']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" 
                                class="form-control <?php echo isset($erreurs['prenom']) ? 'is-invalid' : ''; ?>" 
                                id="prenom" 
                                name="prenom" 
                                value="<?php echo htmlspecialchars($prenom); ?>" 
                                placeholder="Entrez le prénom">
                            <?php if (isset($erreurs['prenom'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['prenom']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-control <?php echo isset($erreurs['type']) ? 'is-invalid' : ''; ?>" 
                                    id="type" 
                                    name="type">
                                <option value="" <?php echo $type === "" ? "selected" : ""; ?>>-- Sélectionnez un type --</option>
                                <option value="1" <?php echo $type === "1" ? "selected" : ""; ?>>Interne</option>
                                <option value="0" <?php echo $type === "0" ? "selected" : ""; ?>>Externe</option>
                            </select>
                            <?php if (isset($erreurs['type'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['type']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="specialite" class="form-label">Spécialité</label>
                            <input type="text" 
                                class="form-control <?php echo isset($erreurs['specialite']) ? 'is-invalid' : ''; ?>" 
                                id="specialite" 
                                name="specialite" 
                                value="<?php echo htmlspecialchars($specialite); ?>" 
                                placeholder="Entrez la spécialité">
                            <?php if (isset($erreurs['specialite'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['specialite']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="motSpecialite" class="form-label">Mots-clés spécialités</label>
                            <input type="text" 
                                class="form-control <?php echo isset($erreurs['motSpecialite']) ? 'is-invalid' : ''; ?>" 
                                id="motSpecialite" 
                                name="motSpecialite" 
                                value="<?php echo htmlspecialchars($motSpecialite); ?>" 
                                placeholder="Entrez jusqu'à 6 mots-clés séparés par des espaces">
                            <?php if (isset($erreurs['motSpecialite'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['motSpecialite']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Numéro de téléphone</label>
                            <input type="tel" 
                                class="form-control <?php echo isset($erreurs['telephone']) ? 'is-invalid' : ''; ?>" 
                                id="telephone" 
                                name="telephone" 
                                value="<?php echo htmlspecialchars($telephone); ?>" 
                                placeholder="Exemple : 0611661388">
                            <?php if (isset($erreurs['telephone'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['telephone']; ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if (isset($erreurs['existance'])): ?>
                            <div class="alert alert-danger"><?php echo $erreurs['existance']; ?></div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modale Modifier Conferencier -->
    <div class="modal fade <?php echo !empty($erreursModif) ? 'show' : ''; ?>" 
        id="modalMofifierConferencier" 
        style="<?php echo !empty($erreursModif) ? 'display: block;' : 'display: none;'; ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalModifierConferencierLabel">Modifier un conférencier</h5>
                    <a href="conferenciers.php" class="btn-close" aria-label="Close"></a>
                </div>
                <div class="modal-body">
                    <form id="formModifierConferencier" method="POST" action="conferenciers.php">
                        <!-- Champ pour l'ID du conférencier (caché) -->
                        <input type="hidden" id="idConferencier" name="idConferencier" value="<?php if(isset($idConferencier)) {echo htmlspecialchars($idConferencier);} ?>">

                        <!-- Champ pour le prénom -->
                        <div class="mb-3">
                            <label for="prenomConferencier" class="form-label">Prénom</label>
                            <input type="text" class="form-control <?php echo isset($erreursModif['prenom']) ? 'is-invalid' : ''; ?>" id="prenomConferencier" name="prenomConferencier" placeholder="Modifiez le prénom" value="<?php if(isset($prenomModif)) {echo htmlspecialchars($prenomModif);} ?>">
                            <?php if (isset($erreursModif['prenom'])) { ?>
                                <div class="invalid-feedback"><?php echo $erreursModif['prenom']; ?></div>
                            <?php } ?>
                        </div>

                        <!-- Champ pour le nom -->
                        <div class="mb-3">
                            <label for="nomConferencier" class="form-label">Nom</label>
                            <input type="text" class="form-control <?php echo isset($erreursModif['nom']) ? 'is-invalid' : ''; ?>" id="nomConferencier" name="nomConferencier" placeholder="Modifiez le nom" value="<?php if(isset($nomModif)) {echo htmlspecialchars($nomModif);} ?>">
                            <?php if (isset($erreursModif['nom'])) { ?>
                                <div class="invalid-feedback"><?php echo $erreursModif['nom']; ?></div>
                            <?php } ?>
                        </div>

                        <!-- Champ pour le numéro de téléphone -->
                        <div class="mb-3">
                            <label for="telephoneConferencier" class="form-label">Numéro de téléphone</label>
                            <input type="tel" class="form-control <?php echo isset($erreursModif['telephone']) ? 'is-invalid' : ''; ?>" id="telephoneConferencier" name="telephoneConferencier" placeholder="Modifiez le numéro de téléphone"  value="<?php if(isset($telephoneModif)) {echo htmlspecialchars($telephoneModif);}?>">
                            <?php if (isset($erreursModif['telephone'])) { ?>
                                <div class="invalid-feedback"><?php echo $erreursModif['telephone']; ?></div>
                            <?php } ?>
                        </div>

                        <!-- Champ pour les mots clés spécialité-->
                        <div class="mb-3">
                            <label for="motsCleSpe" class="form-label">Spécialité</label>
                            <input type="text" class="form-control <?php echo isset($erreursModif['motsCleSpecialite']) ? 'is-invalid' : ''; ?>" id="motsCleSpe" name="motsCleSpe" placeholder="Modifiez les mots-clés de la spécialité du conférencier"  value="<?php if(isset($motSpecialiteModif)) {echo htmlspecialchars($motSpecialiteModif);}?>">
                            <?php if (isset($erreursModif['motsCleSpecialite'])) { ?>
                                <div class="invalid-feedback"><?php echo $erreursModif['motsCleSpecialite']; ?></div>
                            <?php } ?>
                        </div>

                        <?php if (isset($erreursModif['existance'])) { ?>
                                <div class="alert alert-danger"><?php echo $erreursModif['existance']; ?></div>
                        <?php } ?>
                        <!-- Bouton pour soumettre le formulaire -->
                        <button type="submit" name="action" value="modifierConferencier" class="btn btn-primary">Enregistrer les modifications</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modale de Confirmation -->
    <div class="modal <?php echo $conferenciersCree ? 'show' : ''; ?>" 
        id="modalConfirmation" 
        tabindex="-1" 
        aria-labelledby="modalConfirmationLabel" 
        aria-hidden="<?php echo $conferenciersCree ? 'false' : 'true'; ?>" 
        style="<?php echo $conferenciersCree ? 'display: block;' : 'display: none;'; ?>">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmationLabel">Succès</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Conférencier créé avec succès.</p>
                </div>
                <div class="modal-footer">
                    <a href="conferenciers.php" class="btn btn-secondary">Fermer</a>
                </div>
            </div>
        </div>
    </div>
    <?php require("../ressources/footer.php");?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".open-modal").forEach(button => {
                button.addEventListener("click", function () {
                    let idConferencier = this.getAttribute("data-id");
                    document.getElementById("idConferencier").value = idConferencier;
                });
            });
        });
    </script>

</body>
</html>