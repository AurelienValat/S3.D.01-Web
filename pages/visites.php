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

    // Initialisation des erreurs
    $erreurs = [];

    // Indicateur pour savoir si une visite a été créée avec succès
    $visiteCree = false;

    // Vérifie que la requête est de type POST et qu'elle n'est pas destinée à supprimer une visite
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['supprimerVisite'])) {
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
            $erreurs['id_exposition'] = "Veuillez sélectionner une exposition.";
            }
            if ($id_conferencier <= 0) {
            $erreurs['id_conferencier'] = "Veuillez sélectionner un conférencier.";
            }
            if ($id_employe <= 0) {
            $erreurs['id_employe'] = "Veuillez sélectionner un employé.";
            }
            
            // Validation de la date de visite
            if (($date_visite == "") || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $date_visite)) {
                $erreurs['date_visite'] = "Date invalide.";
            } else {
                // Vérification du jour (mardi à dimanche)
                $jour_semaine = date('N', strtotime($date_visite)); // 1 = lundi, 7 = dimanche
                if ($jour_semaine == 1) { // 1 correspond à lundi
                    $erreurs['date_visite'] = "Les visites ne peuvent pas avoir lieu le lundi.";
                } else {
                    // Vérifier si la date est entre aujourd'hui et dans 3 ans
                    $aujourd_hui = new DateTime();
                    $date_max = (clone $aujourd_hui)->modify('+3 years');
                    $date_visite_obj = DateTime::createFromFormat('Y-m-d', $date_visite);
            
                    if (!$date_visite_obj) {
                        $erreurs['date_visite'] = "Format de date incorrect.";
                    } elseif ($date_visite_obj < $aujourd_hui) {
                        $erreurs['date_visite'] = "La date doit être aujourd'hui ou dans le futur.";
                    } elseif ($date_visite_obj > $date_max) {
                        $erreurs['date_visite'] = "La date ne peut pas dépasser 3 ans à partir d'aujourd'hui.";
                    }
                }
            }            
            if (($horaire_debut == "") || !preg_match("/^(?:[01]\d|2[0-3]):[0-5]\d$/", $horaire_debut)) {
                $erreurs['horaire_debut'] = "Heure invalide.";
            } else {
                // Vérification des horaires d'ouverture
                $heure = (int) substr($horaire_debut, 0, 2);
                if ($heure < 9 || $heure >= 18) {
                    $erreurs['horaire_debut'] = "Les visites doivent avoir lieu entre 9 heures et 19 heures.";
                }
            }
    
            if (($intitule_client == "") || strlen($intitule_client) > 50) {
                $erreurs['intitule_client'] = "L’intitulé client est requis et ne doit pas dépasser 50 caractères.";
            }
            if (!preg_match("/^[0-9]{4}$/", $no_tel_client) && $no_tel_client != "") {
                $erreurs['no_tel_client'] = 'Numéro de téléphone invalide. Il doit contenir 4 chiffre.';
            }
            if (empty($erreurs)) {
                if (!verifierDisponibiliteConferencier($pdo, $id_conferencier, $date_visite, $horaire_debut)) {
                    $erreurs['horaire_debut'] = "Le conférencier n’est pas disponible à cet horaire.";
                }
                if (!verifierEspacementVisites($pdo, $id_exposition, $date_visite, $horaire_debut)) {
                    $erreurs['horaire_debut'] = "Les visites doivent être espacées de 10 minutes.";
                }
                if (empty($erreurs)) {
                    creerVisite($pdo, $id_exposition, $id_conferencier, $id_employe, $horaire_debut, $date_visite, $intitule_client, $no_tel_client);
                    $visiteCree = true;
                }
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script>   
    <title>MUSEOFLOW - Gestion des Visites</title>
</head>
<body class="fond">
   
    <?php require("../ressources/navBar.php");?>

    <div class="container content col-12">
        <div class="container-blanc">
            <h1 class="text-center">Gestion des Visites</h1>
            <div class="d-flex justify-content-between align-items-center">
                <!-- Menu Ajouter/Réserver -->
                <button class="btn-action btn-modify btn-blue" data-bs-toggle="modal" data-bs-target="#modalAjouterVisite" id="modalAjouterVisiteLabel">Ajouter/Réserver Visite</button>                 
                <!-- Menu Filtres -->
                <button class="btn btn-light d-flex align-items-center gap-2">
                <i class="fa-solid fa-filter"></i>Filtres
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
    <!-- Modale Ajouter Visite -->
    <div class="modal fade <?php echo !empty($erreurs) ? 'show' : ''; ?>" 
        id="modalAjouterVisite" 
        style="<?php echo !empty($erreurs) ? 'display: block;' : 'display: none;'; ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAjouterVisiteLabel">Ajouter une Visite</h5>
                    <a href="visites.php" class="btn-close" aria-label="Close"></a>
                </div>
                <div class="modal-body">
                    <form id="formAjouterVisite" method="POST" action="visites.php">
                        <div class="mb-3">
                            <label for="id_exposition" class="form-label">Exposition</label>
                            <select id="id_exposition" 
                                    name="id_exposition" 
                                    class="form-control <?php echo isset($erreurs['id_exposition']) ? 'is-invalid' : ''; ?>">
                                <option value="">-- Sélectionnez une exposition --</option>
                                <?php foreach ($expositions as $expo): ?>
                                    <option value="<?= htmlspecialchars($expo['id_exposition']); ?>" 
                                        <?php echo (isset($id_exposition) && $id_exposition == $expo['id_exposition']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($expo['intitule']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($erreurs['id_exposition'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['id_exposition']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="id_conferencier" class="form-label">Conférencier</label>
                            <select id="id_conferencier" 
                                    name="id_conferencier" 
                                    class="form-control <?php echo isset($erreurs['id_conferencier']) ? 'is-invalid' : ''; ?>">
                                <option value="">-- Sélectionnez un conférencier --</option>
                                <?php foreach ($conferenciers as $conf): ?>
                                    <option value="<?= htmlspecialchars($conf['id_conferencier']); ?>" 
                                        <?php echo (isset($id_conferencier) && $id_conferencier == $conf['id_conferencier']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($conf['nom'] . " " . $conf['prenom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($erreurs['id_conferencier'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['id_conferencier']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="id_employe" class="form-label">Employé</label>
                            <select id="id_employe" 
                                    name="id_employe" 
                                    class="form-control <?php echo isset($erreurs['id_employe']) ? 'is-invalid' : ''; ?>">
                                <option value="">-- Sélectionnez un employé --</option>
                                <?php foreach ($employes as $emp): ?>
                                    <option value="<?= htmlspecialchars($emp['id_employe']); ?>" 
                                        <?php echo (isset($id_employe) && $id_employe == $emp['id_employe']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($emp['nom'] . " " . $emp['prenom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($erreurs['id_employe'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['id_employe']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="date_visite" class="form-label">Date</label>
                            <input type="date" 
                                id="date_visite" 
                                name="date_visite" 
                                class="form-control <?php echo isset($erreurs['date_visite']) ? 'is-invalid' : ''; ?>" 
                                value="<?php echo htmlspecialchars($date_visite ?? ''); ?>">
                            <?php if (isset($erreurs['date_visite'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['date_visite']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="horaire_debut" class="form-label">Heure de Début</label>
                            <input type="time" 
                                id="horaire_debut" 
                                name="horaire_debut" 
                                class="form-control <?php echo isset($erreurs['horaire_debut']) ? 'is-invalid' : ''; ?>" 
                                value="<?php echo htmlspecialchars($horaire_debut ?? ''); ?>">
                            <?php if (isset($erreurs['horaire_debut'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['horaire_debut']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="intitule_client" class="form-label">Client</label>
                            <input type="text" 
                                id="intitule_client" 
                                name="intitule_client" 
                                class="form-control <?php echo isset($erreurs['intitule_client']) ? 'is-invalid' : ''; ?>" 
                                value="<?php echo htmlspecialchars($intitule_client ?? ''); ?>">
                            <?php if (isset($erreurs['intitule_client'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['intitule_client']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="no_tel_client" class="form-label">Téléphone</label>
                            <input type="text" 
                                id="no_tel_client" 
                                name="no_tel_client" 
                                class="form-control <?php echo isset($erreurs['no_tel_client']) ? 'is-invalid' : ''; ?>" 
                                value="<?php echo htmlspecialchars($no_tel_client ?? ''); ?>">
                            <?php if (isset($erreurs['no_tel_client'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['no_tel_client']; ?></div>
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Visite créé avec succès.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="visites.php" class="btn btn-secondary">Fermer</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>