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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['supprimerConferencier']) && !isset($_POST['idConferencier'])) {
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
            if ($type == "") {
                $erreurs['type'] = 'Le type est requis.';
            }
            if (($prenom == "") || strlen($prenom) > 50) {
                $erreurs['prenom'] = 'Le prénom est requis et ne doit pas dépasser 50 caractères.';
            }
            if (($nom == "") || strlen($nom) > 50) {
                $erreurs['nom'] = 'Le nom est requis et ne doit pas dépasser 50 caractères.';
            }
            if (($specialite == "") || strlen($specialite) > 50) {
                $erreurs['specialite'] = 'La specialite est requise et ne doit pas dépasser 50 caractères.';
            }
            if (!preg_match("#^[0-9]{10}#", $telephone) or strlen($telephone)>10 ) {
                $erreurs['telephone'] = 'Numéro de téléphone invalide. Il doit contenir 10 chiffres.';
            }
            // if (($indisponibilite_debut != "") || strlen($indisponibilite_debut) > 50) {
            //     $erreurs['indisponibilite_debut'] = '';
            // }
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
                if (($nomModif == "") || strlen($nomModif) > 35) {
                    $erreursModif['nom'] = 'Le nom est requis et ne doit pas dépasser 35 caractères.';
                }
                if (!preg_match("/^[0-9]{10}$/", $telephoneModif)) {
                    $erreursModif['telephone'] = 'Numéro de téléphone invalide. Il doit contenir 10 chiffres.';
                }
                if (($motSpecialiteModif == "") || count(explode(" ", $motSpecialiteModif)) > 6){
                    $erreursModif['motsCleSpecialite'] = 'La spécialité doit contenir entre 1 et 6 mots-clés séparés par des espaces.';
                }

                // Si aucune erreur, mise à jour
                if (empty($erreursModif) && $_POST['action'] === 'modifierConferencier') {
                    if (verifierExistanceConferencier($pdo, $nomModif, $prenomModif, $idConferencier)) {
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
                        echo "<script>alert('Conférencier modifié avec succès.');</script>";
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
    <meta charset="utf-8">  
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script>  
    <title>MUSEOFLOW - Gestion des Conférenciers</title>
</head>
<body class="fond">

    <?php require("../ressources/navBar.php");
    ?>
        <div class="container content">
        <div class="container-blanc">
            <h1 class="text-center">Gestion des Conférenciers</h1>
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn-action btn-modify btn-blue" data-bs-toggle="modal" onclick="resetFormulaire()" data-bs-target="#modalAjouterConferencier" id="modalAjouterConferencierLabel" title="Ajouter un conférencier"><i class="fa-solid fa-user-plus"></i></button>
                <button class="btn btn-light d-flex align-items-center gap-2">
                <i class="fa-solid fa-filter"></i>Filtres
                </button>
            </div>
            <div class="table">
                <table class="table table-striped table-bordered">
                <?php   
                    $conferenciers = getConferenciers($pdo);
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
                                    echo '<button class="btn-action btn-blue" title="Afficher le planning du conférencier">
                                            <i class="fa-solid fa-calendar"></i>
                                        </button>';
                                    
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
                        <!-- <div class="mb-3">
                            <label for="indisponibilites" class="form-label">Indisponibilités</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="date" 
                                        class="form-control <?php //echo isset($erreurs['indisponibilite_debut']) ? 'is-invalid' : ''; ?>" 
                                        id="indisponibilite_debut" 
                                        name="indisponibilite_debut" 
                                        value="<?php //echo htmlspecialchars($indisponibilite_debut); ?>" 
                                        placeholder="Date de début">
                                    <?php //if (isset($erreurs['indisponibilite_debut'])): ?>
                                        <div class="invalid-feedback"><?php //echo $erreurs['indisponibilite_debut']; ?></div>
                                    <?php //endif; ?>
                                </div> -->
                                <!-- <div class="col-6">
                                    <input type="date" 
                                        class="form-control <?php //echo isset($erreurs['indisponibilite_fin']) ? 'is-invalid' : ''; ?>" 
                                        id="indisponibilite_fin" 
                                        name="indisponibilite_fin" 
                                        value="<?php //echo htmlspecialchars($indisponibilite_fin); ?>" 
                                        placeholder="Date de fin">
                                    <?php //if (isset($erreurs['indisponibilite_fin'])): ?>
                                        <div class="invalid-feedback"><?php// echo $erreurs['indisponibilite_fin']; ?></div>
                                    <?php //endif; ?>
                                </div>
                            </div>
                        </div> -->
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
                    <input type="hidden" id="idConferencier" name="idConferencier" value="<?php echo htmlspecialchars($idConferencier) ?>">

                    <!-- Champ pour le prénom -->
                    <div class="mb-3">
                        <label for="prenomConferencier" class="form-label">Prénom</label>
                        <input type="text" class="form-control <?php echo isset($erreursModif['prenom']) ? 'is-invalid' : ''; ?>" id="prenomConferencier" name="prenomConferencier" placeholder="Modifiez le prénom" value="<?php echo htmlspecialchars($prenomModif); ?>">
                        <?php if (isset($erreursModif['prenom'])) { ?>
                            <div class="invalid-feedback"><?php echo $erreursModif['prenom']; ?></div>
                        <?php } ?>
                    </div>

                    <!-- Champ pour le nom -->
                    <div class="mb-3">
                        <label for="nomConferencier" class="form-label">Nom</label>
                        <input type="text" class="form-control <?php echo isset($erreursModif['nom']) ? 'is-invalid' : ''; ?>" id="nomConferencier" name="nomConferencier" placeholder="Modifiez le nom" value="<?php echo htmlspecialchars($nomModif); ?>">
                        <?php if (isset($erreursModif['nom'])) { ?>
                            <div class="invalid-feedback"><?php echo $erreursModif['nom']; ?></div>
                        <?php } ?>
                    </div>

                    <!-- Champ pour le numéro de téléphone -->
                    <div class="mb-3">
                        <label for="telephoneConferencier" class="form-label">Numéro de téléphone</label>
                        <input type="tel" class="form-control <?php echo isset($erreursModif['telephone']) ? 'is-invalid' : ''; ?>" id="telephoneConferencier" name="telephoneConferencier" placeholder="Modifiez le numéro de téléphone"  value="<?php echo htmlspecialchars($telephoneModif);?>">
                        <?php if (isset($erreursModif['telephone'])) { ?>
                            <div class="invalid-feedback"><?php echo $erreursModif['telephone']; ?></div>
                        <?php } ?>
                    </div>

                    <!-- Champ pour les mots clés spécialité-->
                    <div class="mb-3">
                        <label for="motsCleSpe" class="form-label">Spécialité</label>
                        <input type="text" class="form-control <?php echo isset($erreursModif['motsCleSpecialite']) ? 'is-invalid' : ''; ?>" id="motsCleSpe" name="motsCleSpe" placeholder="Modifiez les mots-clés de la spécialité du conférencier"  value="<?php echo htmlspecialchars($motSpecialiteModif);?>">
                        <?php if (isset($erreursModif['motsCleSpecialite'])) { ?>
                            <div class="invalid-feedback"><?php echo $erreursModif['motsCleSpecialite']; ?></div>
                        <?php } ?>
                    </div>

                    <!-- Affichage des indisponibilités -->
                    <?php
                        if (isset($_POST['action']) && $_POST['action'] === 'voirIndisponibilites' && !empty($_POST['idConferencier'])) {
                            $idConferencier = intval($_POST['idConferencier']); 

                            // Récupérer les indisponibilités depuis la base de données
                            $stmt = recupIndisponibilite($pdo, $idConferencier);
                            echo "<h5>Indisponibilités du conférencier sélectionné :</h5>";
                            if ($stmt->rowCount() > 0) {
                                echo "<table class='table table-striped'>";
                                echo "<thead><tr><th>Date de début</th><th>Date de fin</th></tr></thead>";
                                echo "<tbody>";
                                while ($row = $stmt->fetch()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['debut']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['fin']) . "</td>";
                                    echo "<td><button type='button' class='btn btn-danger'>Supprimer</button></td>"; 
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                                echo "</table>";
                            } else {
                                echo "<p>Aucune indisponibilité trouvée pour ce conférencier.</p>";
                            }
                        }
                    ?>

                    <!-- Bouton pour voir les indisponibilités -->
                    <button type="submit" name="action" value="voirIndisponibilites" class="btn btn-primary mt-2">Voir indisponibilités</button>

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
    
</body>
<script>
    /**
     * Remplit automatiquement le formulaire de modification des conférenciers avec les données fournies.
     * @param {number} idUtilisateur - Identifiant du conférencier.
     * @param {string} prenom - Prénom du conférencier.
     * @param {string} nom - Nom du conférencier.
     * @param {string} telephone - Numéro de téléphone.
     */
    function remplirFormulaire(idConferencier, prenom, nom, telephone, motsCles) {
        // Remplir les champs du formulaire
        document.getElementById('idConferencier').value = idConferencier;   
        document.getElementById('prenomConferencier').value = prenom;
        document.getElementById('nomConferencier').value = nom;
        document.getElementById('telephoneConferencier').value = telephone;
        document.getElementById('motsCleSpe').value = motsCles;
    }

    //Pour que la modale se re-ouvre automatiquement après clic sur le bouton voirIndisponibilites
    <?php if (isset($_POST['action']) && $_POST['action'] === 'voirIndisponibilites') { ?>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = new bootstrap.Modal(document.getElementById('modalMofifierConferencier'));
            modal.show();
        });
    <?php } ?>

    // function resetFormulaire() {
    // document.getElementById("formAjouterConferencier").reset(); // Réinitialise tous les champs du formulaire
    // document.getElementById("prenom").value = ""; // Exemple : Efface le prénom
    // document.getElementById("nom").value = ""; // Exemple : Efface le nom
    // document.getElementById("telephone").value = ""; // Exemple : Efface le numéro de téléphone
    // }

</script>
</html>