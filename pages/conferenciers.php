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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['supprimerConferencier'])) {
    try {
        // Initialisation des variables de formulaire
        $type = isset($_POST['type']) ? trim($_POST['type']) : "";
        $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : "";
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : "";
        $specialite = isset($_POST['specialite']) ? trim($_POST['specialite']) : "";
        $motSpecialite = isset($_POST['motSpecialite']) ? trim($_POST['motSpecialite']) : "";
        $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : "";

        // Validation des champs
        if (($motSpecialite == "") || strlen($motSpecialite) > 10) {
            $erreurs['motSpecialite'] = 'Mot clés spécialié (max 6 mots).';
        }
        if ($type == "") {
            $erreurs['type'] = 'Le type est requis.';
        }
        if (($prenom == "") || strlen($prenom) > 35) {
            $erreurs['prenom'] = 'Le prénom est requis et ne doit pas dépasser 35 caractères.';
        }
        if (($nom == "") || strlen($nom) > 35) {
            $erreurs['nom'] = 'Le nom est requis et ne doit pas dépasser 35 caractères.';
        }
        if (($specialite == "") || strlen($specialite) > 25) {
            $erreurs['specialite'] = 'La specialite est requise et ne doit pas dépasser 25 caractères.';
        }
        if (!preg_match("/^[0-9]{4}$/", $telephone) && $telephone != "") {
            $erreurs['telephone'] = 'Numéro de téléphone invalide. Il doit contenir 4 chiffre.';
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

    <?php require("../ressources/navBar.php");?>
    
        <div class="container content">
        <div class="container-blanc">
            <h1 class="text-center">Gestion des Conférenciers</h1>
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn-action btn-modify btn-blue" data-bs-toggle="modal" data-bs-target="#modalAjouterConferencier" id="modalAjouterConferencierLabel">Ajouter un Conférencier</button>
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
    <!-- Modale Ajouter Utilisateur -->
    <div class="modal fade <?php echo !empty($erreurs) ? 'show' : ''; ?>" 
        id="modalAjouterConferencier" 
        style="<?php echo !empty($erreurs) ? 'display: block;' : 'display: none;'; ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAjouterConferencierLabel">Ajouter un Conferencier</h5>
                    <a href="conferenciers.php" class="btn-close" aria-label="Close"></a>
                </div>
                <div class="modal-body">
                    <form id="formAjouterConferencier" method="POST" action="conferenciers.php">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom </label>
                            <input type="text" class="form-control <?php echo isset($erreurs['nom']) ? 'is-invalid' : ''; ?>" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>">
                            <?php if (isset($erreurs['nom'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['nom']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control <?php echo isset($erreurs['prenom']) ? 'is-invalid' : ''; ?>" id="prenom" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>">
                            <?php if (isset($erreurs['prenom'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['prenom']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3"> <!-- changer de value avec str to int pour externe et intern -->
                            <label for="type" class="form-label">Type</label>
                            <select class="form-control <?php echo isset($erreurs['type']) ? 'is-invalid' : ''; ?>" id="type" name="type">
                                <option value="" <?php echo $type === "" ? "selected" : ""; ?>>-- Sélectionnez un type --</option>
                                <option value="1" <?php echo $type === "1" ? "selected" : ""; ?>>Interne</option>
                                <option value="0" <?php echo $type === "0" ? "selected" : ""; ?>>Externe</option>
                            </select>
                            <?php if (isset($erreurs['type'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['type']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="specialite" class="form-label">Specialite</label>
                            <input type="texte" class="form-control <?php echo isset($erreurs['specialite']) ? 'is-invalid' : ''; ?>" id="specialite" name="specialite" value="<?php echo htmlspecialchars($specialite); ?>">
                            <?php if (isset($erreurs['specialite'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['specialite']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="motSpecialite" class="form-label">Mots clés spécialités</label>
                            <input type="texte" class="form-control <?php echo isset($erreurs['motSpecialite']) ? 'is-invalid' : ''; ?>" id="motSpecialite" name="motSpecialite" value="<?php echo htmlspecialchars($motSpecialite); ?>">
                            <?php if (isset($erreurs['motSpecialite'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['motSpecialite']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Numéro de téléphone</label>
                            <input type="tel" class="form-control <?php echo isset($erreurs['telephone']) ? 'is-invalid' : ''; ?>" id="telephone" name="telephone" value="<?php echo htmlspecialchars($telephone); ?>">
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
    <!-- Modale de Confirmation -->
    <div class="modal <?php echo $utilisateurCree ? 'show' : ''; ?>" 
        id="modalConfirmation" 
        tabindex="-1" 
        aria-labelledby="modalConfirmationLabel" 
        aria-hidden="<?php echo $utilisateurCree ? 'false' : 'true'; ?>" 
        style="<?php echo $utilisateurCree ? 'display: block;' : 'display: none;'; ?>">
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
</div>
</body>
</html>