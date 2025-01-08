<?php 
    session_start();

    require ('../bdd/fonctions.php');
    require ('../bdd/connecterBD.php');
    require ('../bdd/requetes.php');
    require ('../ressources/verifVisites.php');
    verifSession(); // Vérifie si une session valide existe

    $estAdmin = isset($_SESSION['est_admin']) && $_SESSION['est_admin'] == 1;
    
    $pdo = initierConnexion();
    
    if ($pdo == FALSE) {
        header("Location: erreurs/erreurBD.php");
    }
    
    // Vérification si une suppression est demandée
    if (isset($_POST['supprimerVisite']) && $_POST['supprimerVisite'] != trim('')) {
        $userIdToDelete = intval($_POST['supprimerVisite']); // Sécuriser la donnée
        
        try {
            supprimerLigne($pdo, $userIdToDelete, "Visite");
        } catch (PDOException) {
            $_SESSION['donneeEnErreur'] = 'visite';
            $_SESSION['cheminDernierePage'] = '/S3.D.01-Web/pages/vivites.php';
            header("Location: ./erreurs/impossibleDeTraiterVotreDemande.php");
        }
    }

    // Initialisation des erreurs pour l'ajout de visite
    $erreurs_ajout = [];

    // Indicateur pour savoir si une visite a été créée avec succès
    $visiteCree = false;


    // Vérifie que la requête est de type POST et qu'elle n'est pas destinée à supprimer une visite
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['supprimerVisite']) && isset($_POST['type_formulaire']) && $_POST['type_formulaire'] === 'ajout' && !isset($_POST['demandeFiltrage'])) {
        try {
            $id_exposition = isset($_POST['id_exposition']) ? (int) $_POST['id_exposition'] : 0;
            $id_conferencier = isset($_POST['id_conferencier']) ? (int) $_POST['id_conferencier'] : 0;
            $id_employe = isset($_POST['id_employe']) ? (int) $_POST['id_employe'] : 0;
            $date_visite = isset($_POST['date_visite']) ? trim($_POST['date_visite']) : "";
            $horaire_debut = isset($_POST['horaire_debut']) ? trim($_POST['horaire_debut']) : "";
            $intitule_client = isset($_POST['intitule_client']) ? trim($_POST['intitule_client']) : "";
            $no_tel_client = isset($_POST['no_tel_client']) ? trim($_POST['no_tel_client']) : "";
            
            // Validation des identifiants pour s'assurer qu'ils sont valides
            if ($id_exposition <= 0) {
                $erreurs_ajout['id_exposition'] = "Veuillez sélectionner une exposition.";
            }
            if ($id_conferencier <= 0) {
                $erreurs_ajout['id_conferencier'] = "Veuillez sélectionner un conférencier.";
            }
            if ($id_employe <= 0) {
                $erreurs_ajout['id_employe'] = "Veuillez sélectionner un employé.";
            }
            
            $erreurs_ajout = verifVisites($pdo, $erreurs_ajout, $horaire_debut, $intitule_client, $no_tel_client, $id_conferencier, $date_visite, $id_exposition);
            
            if (empty($erreurs_ajout)) {
                creerVisite($pdo, $id_exposition, $id_conferencier, $id_employe, $horaire_debut, $date_visite, $intitule_client, $no_tel_client);
                $visiteCree = true;
            }
            
        } catch (Exception $e) {
            echo "<p style='color:red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    
    // Vérif des données pour la modif de visite
    // Tableau des erreurs éventuelles
    $erreurs_modif = [];
    // Vérifie que la requête est de type POST et qu'elle n'est pas destinée à supprimer une visite ni à un ajout
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['supprimerVisite']) && isset($_POST['type_formulaire']) && $_POST['type_formulaire'] === 'modif') {
        try {
            $exposition_concernee_modifie = isset($_POST['intitule_Modif']) ? $_POST['intitule_Modif'] : "";
            $conferencier_modifie = isset($_POST['id_conferencier_Modif']) ? $_POST['id_conferencier_Modif'] : "";
            $id_employe_modifie = isset($_POST['id_employe_Modif']) ? $_POST['id_employe_Modif'] : "";
            $intitule_client_modifie = isset($_POST['intitule_client_Modif']) ? $_POST['intitule_client_Modif'] : "";
            $no_tel_client_modifie = isset($_POST['no_tel_client_Modif']) ? trim($_POST['no_tel_client_Modif']) : "";
            $date_visite_modifie = isset($_POST['date_visite_Modif']) ? trim($_POST['date_visite_Modif']) : "";
            $horaire_debut_modifie = isset($_POST['horaire_debut_Modif']) ? trim($_POST['horaire_debut_Modif']) : "";
            
            // Validation des identifiants pour s'assurer qu'ils sont valides
            if ($exposition_concernee_modifie === "" || $exposition_concernee_modifie === "Sélectionner dans la liste") {
                $erreurs_modif['id_exposition'] = "Veuillez sélectionner une exposition.";
            }
          
            if ($conferencier_modifie === "" || $conferencier_modifie === "Sélectionner dans la liste") {
                $erreurs_modif['id_conferencier'] = "Veuillez sélectionner un conférencier.";
            }
            
            if ($id_employe_modifie === "" || $id_employe_modifie === "Sélectionner dans la liste") {
                $erreurs_modif['id_employe'] = "Veuillez sélectionner un employé.";
            }
            
            $erreurs_modif = verifVisites($pdo, $erreurs_modif, $horaire_debut_modifie, $intitule_client_modifie, $no_tel_client_modifie, $conferencier_modifie, $date_visite_modifie, $exposition_concernee_modifie);
            
            // S'il n'y a pas d'erreurs
            if (empty($erreurs_modif)) {
                modifierVisite($pdo, $exposition_concernee_modifie, $conferencier_modifie, $id_employe_modifie, $intitule_client_modifie, $no_tel_client_modifie, $date_visite_modifie, $horaire_debut_modifie, $_POST['id_visite_Modif']);
            }
            
        } catch (Exception $e) {
            echo "<p style='color:red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    $expositions = getExpositions($pdo);
    $conferenciers = getConferenciers($pdo);
    $employes = getUtilisateurs($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">  
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="../js/visites.js" type="text/javascript"></script>
    <title>MUSEOFLOW - Gestion des Visites</title>
</head>
<body class="fond">
   
    <?php 
    require("../ressources/navBar.php");
    require("../ressources/filtres.php");
    // Pour afficher les options de filtrages spécifiques aux visites
    $_SESSION['filtreAApliquer'] = 'visites';
    ?>

    <div class="container content col-12">
        <div class="container-blanc">
            <h1 class="text-center">Gestion des Visites</h1>
            <div class="d-flex justify-content-between align-items-center">
                <!-- Menu Ajouter/Réserver -->
                <button class="btn-action btn-modify btn-blue"  title="Réserver une visite" data-bs-toggle="modal" data-bs-target="#modalAjouterVisite" id="modalAjouterVisiteLabel"><i class="fa-solid fa-plus"></i></button>                 
                <!-- Menu Filtres -->
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
                                    echo "<button class='btn-action btn-modify btn-blue' 
                                                  data-bs-toggle='modal' 
                                                  data-bs-target='#modifModal' 
                                                  data-id='".$ligne['id_visite']."' 
                                                  onclick='remplirModalModif(
                                                    " . intval($ligne['id_visite']) . ", 
                                                    \"" . addslashes($ligne['intitule_client']) . "\",
                                                    \"" . addslashes($ligne['no_tel_client']) . "\",
                                                    \"" . addslashes($ligne['date_visite']) . "\",
                                                    \"" . addslashes($ligne['horaire_debut']) . "\"
                                                  )'>
                                                  <i class='fa-solid fa-pencil' aria-hidden='true'></i>
                                          </button>";?>
                                        <form method="POST" action= "visites.php" style="display:inline;">
                                        <?php echo "<input type='hidden' name='supprimerVisite' value='" . $ligne['id_visite'] . "'>";
                                        ?> <button type="submit" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette visite ?');"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                        <?php 
                                    echo "</td>";
                                echo "</tr>";
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


    <!-- Modal Ajouter Visite -->
    <div class="modal fade <?php echo !empty($erreurs_ajout) ? 'show' : ''; ?>" 
        id="modalAjouterVisite" 
        style="<?php echo !empty($erreurs_ajout) ? 'display: block;' : 'display: none;'; ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAjouterVisiteLabel">Ajouter une Visite</h5>
                    <a href="visites.php" class="btn-close" aria-label="Close"></a>
                </div>
                <div class="modal-body">
                    <form id="formAjouterVisite" method="POST" action="visites.php">
                    <!-- Pour ne pas déclencher l'ajout et la modification en même temps -->
                    <input type="hidden" id="type_formulaire" name="type_formulaire" value="ajout">
                        <div class="mb-3">
                            <label for="id_exposition" class="form-label">Exposition</label>
                            <select id="id_exposition" 
                                    name="id_exposition" 
                                    class="form-control <?php echo isset($erreurs_ajout['id_exposition']) ? 'is-invalid' : ''; ?>">
                                <option value="">-- Sélectionnez une exposition --</option>
                                <?php foreach ($expositions as $expo): ?>
                                    <option value="<?= htmlspecialchars($expo['id_exposition']); ?>" 
                                        <?php echo (isset($id_exposition) && $id_exposition == $expo['id_exposition']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($expo['intitule']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($erreurs_ajout['id_exposition'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs_ajout['id_exposition']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="id_conferencier" class="form-label">Conférencier</label>
                            <select id="id_conferencier" 
                                    name="id_conferencier" 
                                    class="form-control <?php echo isset($erreurs_ajout['id_conferencier']) ? 'is-invalid' : ''; ?>">
                                <option value="">-- Sélectionnez un conférencier --</option>
                                <?php foreach ($conferenciers as $conf): ?>
                                    <option value="<?= htmlspecialchars($conf['id_conferencier']); ?>" 
                                        <?php echo (isset($id_conferencier) && $id_conferencier == $conf['id_conferencier']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($conf['nom'] . " " . $conf['prenom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($erreurs_ajout['id_conferencier'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs_ajout['id_conferencier']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="id_employe" class="form-label">Employé</label>
                            <select id="id_employe" 
                                    name="id_employe" 
                                    class="form-control <?php echo isset($erreurs_ajout['id_employe']) ? 'is-invalid' : ''; ?>">
                                <option value="">-- Sélectionnez un employé --</option>
                                <?php foreach ($employes as $emp): ?>
                                    <option value="<?= htmlspecialchars($emp['id_employe']); ?>" 
                                        <?php echo (isset($id_employe) && $id_employe == $emp['id_employe']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($emp['nom'] . " " . $emp['prenom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($erreurs_ajout['id_employe'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs_ajout['id_employe']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="date_visite" class="form-label">Date</label>
                            <input type="date" 
                                id="date_visite" 
                                name="date_visite" 
                                class="form-control <?php echo isset($erreurs_ajout['date_visite']) ? 'is-invalid' : ''; ?>" 
                                value="<?php echo htmlspecialchars($date_visite ?? ''); ?>">
                            <?php if (isset($erreurs_ajout['date_visite'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs_ajout['date_visite']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="horaire_debut" class="form-label">Heure de Début</label>
                            <input type="time" 
                                id="horaire_debut" 
                                name="horaire_debut" 
                                class="form-control <?php echo isset($erreurs_ajout['horaire_debut']) ? 'is-invalid' : ''; ?>" 
                                value="<?php echo htmlspecialchars($horaire_debut ?? ''); ?>">
                            <?php if (isset($erreurs_ajout['horaire_debut'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs_ajout['horaire_debut']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="intitule_client" class="form-label">Client</label>
                            <input type="text" 
                                id="intitule_client" 
                                name="intitule_client" 
                                class="form-control <?php echo isset($erreurs_ajout['intitule_client']) ? 'is-invalid' : ''; ?>" 
                                value="<?php echo htmlspecialchars($intitule_client ?? ''); ?>">
                            <?php if (isset($erreurs_ajout['intitule_client'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs_ajout['intitule_client']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="no_tel_client" class="form-label">Téléphone</label>
                            <input type="text" 
                                id="no_tel_client" 
                                name="no_tel_client" 
                                class="form-control <?php echo isset($erreurs_ajout['no_tel_client']) ? 'is-invalid' : ''; ?>" 
                                value="<?php echo htmlspecialchars($no_tel_client ?? ''); ?>">
                            <?php if (isset($erreurs_ajout['no_tel_client'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs_ajout['no_tel_client']; ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if (isset($erreurs_ajout['existance'])): ?>
                            <div class="alert alert-danger"><?php echo $erreurs_ajout['existance']; ?></div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
        <!-- Modale de Confirmation -->
        <div class="modal <?php echo $visiteCree ? 'show' : ''; ?>" 
            id="modalConfirmation" 
            tabindex="-1" 
            aria-labelledby="modalConfirmationLabel" 
            aria-hidden="<?php echo $visiteCree ? 'false' : 'true'; ?>" 
            style="<?php echo $visiteCree ? 'display: block;' : 'display: none;'; ?>">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalConfirmationLabel">Succès</h5>
                        <a href="visites.php">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </a>
                    </div>
                    <div class="modal-body">
                        <p>Visite créée avec succès.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="visites.php" class="btn btn-secondary">Fermer</a>
                    </div>
                </div>
            </div>
        </div>

<!-- Modal Bootstrap pour modifier une visite -->
<div class="modal fade <?php echo !empty($erreurs_modif) ? 'show' : ''; ?>" id="modifModal" aria-labelledby="modifModalLabel" style="<?php echo !empty($erreurs_modif) ? 'display: block;' : 'display: none;'; ?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modifModalLabel">Modifier la visite</h5>
          <a href="visites.php" class="btn-close" aria-label="Close"></a>
      </div>
      <div class="modal-body">
        <form id="formModifVisite" method="post" action="visites.php">
          <input type="hidden" id="id_visite_Modif" name="id_visite_Modif" value="">
          <!-- Pour ne pas déclencher l'ajout et la modification en même temps -->
          <input type="hidden" id="type_formulaire" name="type_formulaire" value="modif">
          <div class="mb-3">
            <label for="intitule_Modif" class="form-label">Exposition concernée</label>
              <select class="form-control <?php echo isset($erreurs_modif['id_exposition']) ? 'is-invalid' : ''; ?>" id="intitule_Modif" name="intitule_Modif" required>
              <option value="Sélectionner dans la liste">--- Sélectionner dans la liste ---</option>
              <!-- Options des conférenciers remplies dynamiquement -->
              <?php 
              $expositions = getExpositions($pdo);
              if (!empty($expositions)) {
                  foreach ($expositions as $exposition) {
                      echo "<option value='".htmlentities($exposition["intitule"], ENT_QUOTES)."'";
                      // Trim car un espace se balade
                      if(isset($_POST['intitule_Modif']) && trim($_POST['intitule_Modif']) === $exposition["intitule"]) {
                          echo ' selected';
                      }
                      echo ">".htmlentities($exposition["intitule"], ENT_QUOTES)."</option>";
                  }
              }?>
            </select>
            <?php if (isset($erreurs_modif['id_exposition'])): ?>
                <div class="invalid-feedback"><?php echo $erreurs_modif['id_exposition']; ?></div>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label for="id_conferencier_Modif" class="form-label">Conférencier assurant la visite</label>
            <select class="form-control <?php echo isset($erreurs_modif['id_conferencier']) ? 'is-invalid' : ''; ?>" id="id_conferencier_Modif" name="id_conferencier_Modif" required>
              <option value="Sélectionner dans la liste">--- Sélectionner dans la liste ---</option>
              <!-- Options des conférenciers remplies dynamiquement -->
              <?php 
              $conferenciers = getConferenciers($pdo);
              if (!empty($conferenciers)) {
                  foreach ($conferenciers as $conferencier) {
                      $nom_prenom = htmlentities($conferencier["nom"], ENT_QUOTES)." ".htmlentities($conferencier["prenom"]);
                      echo "<option value='".$nom_prenom."' ";
                      // On ne réutilise pas $nom_prenom car ils peuvent contenir des caractères convertis en html entities qui faussent la comparaison
                      if(isset($_POST['id_conferencier_Modif']) && $_POST['id_conferencier_Modif'] === $conferencier["nom"].' '.$conferencier["prenom"]) {
                          echo 'selected';
                      }
                      echo ">".$nom_prenom." "."</option>";
                  }
              }?>
            </select>
            <?php if (isset($erreurs_modif['id_conferencier'])): ?>
                <div class="invalid-feedback"><?php echo $erreurs_modif['id_conferencier']; ?></div>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label for="id_employe_Modif" class="form-label">Employé</label>
            <select class="form-control <?php echo isset($erreurs_modif['id_employe']) ? 'is-invalid' : ''; ?>" id="id_employe_Modif" name="id_employe_Modif" required>
              <option value="Sélectionner dans la liste">--- Sélectionner dans la liste ---</option>
              <!-- Options des employés remplies dynamiquement -->
              <?php 
              $utilisateurs = getUtilisateurs($pdo);
              if (!empty($utilisateurs)) {
                  foreach ($utilisateurs as $utilisateur) {
                      $nom_prenom = htmlentities($utilisateur["prenom"], ENT_QUOTES). " " .htmlentities($utilisateur["nom"], ENT_QUOTES);
                      echo "<option value='".$nom_prenom."'";
                      // On ne réutilise pas $nom_prenom car ils peuvent contenir des caractères convertis en html entities qui faussent la comparaison
                      if(isset($_POST['id_employe_Modif']) && $_POST['id_employe_Modif'] === $utilisateur["prenom"].' '.$utilisateur["nom"]) {
                          echo ' selected';
                      }
                      echo ">".$nom_prenom."</option>
              ";
                  }
              }?>
            </select>
            <?php if (isset($erreurs_modif['id_employe'])): ?>
                <div class="invalid-feedback"><?php echo $erreurs_modif['id_employe']; ?></div>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label for="intitule_client_Modif" class="form-label">Client ayant réservé</label>
            <input type="text" class="form-control <?php echo isset($erreurs_modif['intitule_client']) ? 'is-invalid' : ''; ?>" id="intitule_client_Modif" name="intitule_client_Modif" value="" required>
            <?php if (isset($erreurs_modif['intitule_client'])): ?>
                <div class="invalid-feedback"><?php echo $erreurs_modif['intitule_client']; ?></div>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label for="no_tel_client_Modif" class="form-label">Téléphone du client</label>
            <input type="text" class="form-control <?php echo isset($erreurs_modif['no_tel_client']) ? 'is-invalid' : ''; ?>" id="no_tel_client_Modif" name="no_tel_client_Modif" value="" required>
            <?php if (isset($erreurs_modif['no_tel_client'])): ?>
                <div class="invalid-feedback"><?php echo $erreurs_modif['no_tel_client']; ?></div>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label for="date_visite_Modif" class="form-label">Date de la visite</label>
            <input type="date" class="form-control <?php echo isset($erreurs_modif['date_visite']) ? 'is-invalid' : ''; ?>" id="date_visite_Modif" name="date_visite_Modif" value="" required>
            <?php if (isset($erreurs_modif['date_visite'])): ?>
                <div class="invalid-feedback"><?php echo $erreurs_modif['date_visite']; ?></div>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label for="horaire_debut_Modif" class="form-label">Heure de début</label>
            <input type="time" class="form-control <?php echo isset($erreurs_modif['horaire_debut']) ? 'is-invalid' : ''; ?>" id="horaire_debut_Modif" name="horaire_debut_Modif" value="" required>
            <?php if (isset($erreurs_modif['horaire_debut'])): ?>
                <div class="invalid-feedback"><?php echo $erreurs_modif['horaire_debut']; ?></div>
            <?php endif; ?>
          </div>
          <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php 
// On ré-affiche les valeurs précédentes en cas de saisie incorrecte
if (!empty($erreurs_modif)) {
    // On appelle le script de ré-affichage de la saisie
    echo "<script>remplirModalModif(\"".$_POST['id_visite_Modif']
                                    ."\",\"". $_POST['intitule_client_Modif']
                                    ."\",\"". $_POST['no_tel_client_Modif']
                                    ."\",\"". $_POST['date_visite_Modif']
                                    ."\",\"". $_POST['horaire_debut_Modif']
                                    ."\");</script>\n";
}?>

<?php require("../ressources/footer.php"); var_dump($_POST);?>
</body>
</html>