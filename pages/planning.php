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

// Vérifier si le formulaire a été soumis avec l'ID du conférencier
if (isset($_POST['idConferencier'])) {
    $_SESSION['idConferencier'] = intval($_POST['idConferencier']); // Mise à jour de la session
}

if (isset($_SESSION['idConferencier'])) {
    $idConferencier = $_SESSION['idConferencier'];
} else {
    die("Aucun conférencier sélectionné.");
}
    
$indisponibilites = recupIndisponibilite($pdo, $idConferencier);
$visites = recupVisites($pdo, $idConferencier);
$nom = recupNomConferencier($pdo, $idConferencier);

// Suppression 
if (!empty($_POST['supprimerIndisponibilite'])) {
    $indispoASuppr = intval($_POST['supprimerIndisponibilite']); // Sécuriser la donnée    
    try {
        supprimerLigne($pdo, $indispoASuppr, "Indisponibilite");
        header("Location: planning.php");
        exit(); // Arrêter l'exécution du script
    } catch (PDOException) {
        $_SESSION['donneeEnErreur'] = 'indisponibilite';
        $_SESSION['cheminDernierePage'] = '/S3.D.01-Web/pages/planning.php';
        header("Location: ./erreurs/impossibleDeTraiterVotreDemande.php");
    }
}


// Ajout d'une indisponibilité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formType']) && $_POST['formType'] === 'ajouterIndispo' && !isset($_POST['supprimerIndisponibilite'])){
    try {
        // Initialisation des variables de formulaire
        $debut = isset($_POST['debutIndispo']) ? trim($_POST['debutIndispo']) : "";
        $fin = isset($_POST['finIndispo']) ? trim($_POST['finIndispo']) : "";

        // Validation des champs
        if ($debut == "" || $fin == "" ) {
            $erreurs['vide'] = 'Veuillez remplir les 2 dates.';
        }

        if ($debut > $fin){
            $erreurs['superieur'] = 'La date de fin ne peut pas être inférieure à la date de début.';
        }

        if (empty($erreurs)) {
            creerIndisponibilite($pdo, $idConferencier, $debut, $fin);

            header("Location: planning.php?message=" . urlencode("Indisponibilité créée avec succès."));
            exit();
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'>Une erreur est survenue : " . $e->getMessage() . "</p>";
    }
}

// Modifier une indisponibilité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formType']) && $_POST['formType'] === 'modifierIndispo' && !isset($_POST['supprimerIndisponibilite'])){

    try {
        // Initialisation des variables de formulaire
        $debut = isset($_POST['debut']) ? trim($_POST['debut']) : "";
        $fin = isset($_POST['fin']) ? trim($_POST['fin']) : "";
        $idIndisponibilite = intval($_POST['idIndisponibilite']);

        
        // Validation des champs
        if ($debut == "" || $fin == "" ) {
            $erreursModifier['vide'] = 'Veuillez remplir les 2 dates.';
        }

        if ($debut > $fin){
            $erreursModifier['superieur'] = 'La date de fin ne peut pas être inférieure à la date de début.';
        }

        if (empty($erreursModifier)) {
            modifierIndisponibilite($pdo, $idIndisponibilite, $idConferencier, $debut, $fin);
            header("Location: planning.php?message=" . urlencode("Indisponibilité créée avec succès."));
            exit();
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'>Une erreur est survenue : " . $e->getMessage() . "</p>";
    }
}



?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link href="../css/style.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script>
    <title>Planning du Conférencier</title>
</head>
<body>
<?php require("../ressources/navBar.php"); ?>
    <div class="container content">
        <div class="container-blanc">
            <?php
            if (isset($_GET['message']) && $_GET['message'] === "Indisponibilité créée avec succès.") {
                echo "<script>alert('" . addslashes($_GET['message']) . "');</script>";
            }
            ?>

            <a href="conferenciers.php" class="btn btn-secondary mt-3"><i class="fa-solid fa-arrow-left"></i></a>

            <h2>Planning du Conférencier <?php echo htmlspecialchars($nom['prenom'] . " " . $nom['nom']) ?></h2>
            
            <?php if (count($indisponibilites) != 0){ ?>
                <p>Vous pouvez cliquer sur une indisponibilité pour la modifier.</p>
            <?php } ?>
            
            <!-- Tableau des indisponibilités -->
            <?php if (count($indisponibilites) == 0){ ?>
                <p>Aucune indisponibilité enregistrée.</p>
            <?php } ?>

            <button class="btn-action btn-modify btn-blue" data-bs-toggle="modal" data-bs-target="#modalAjouterIndisponibilite" id="modalAjouterIndisponibiliteLabel" title="Ajouter une indisponibilité"><i class="fa-solid fa-user-plus"></i></button>


            <!-- Conteneur du calendrier -->
            <div id="calendar"></div>

            <?php if (count($indisponibilites) > 0){ ?>
                <h3>Liste des indisponibilités :</h3>
                <table class='table table-striped'>
                    <thead>
                        <tr>
                            <th>Début de l'indisponibilité</th>
                            <th>Fin de l'indisponibilité</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($indisponibilites as $indispo){ ?>
                            <tr>
                                <td><?php echo htmlspecialchars($indispo['debut']); ?></td>
                                <td><?php echo htmlspecialchars($indispo['fin']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            <?php } ?>

            <!-- Modale Ajouter Indisponibilité -->
            <div class="modal fade <?php echo !empty($erreurs) ? 'show' : ''; ?>" id="modalAjouterIndisponibilite" style ="<?php echo !empty($erreurs) ? 'display: block;' : 'display: none;'?>" tabindex="-1" aria-labelledby="modalAjouterIndisponibiliteLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalAjouterIndisponibiliteLabel">Ajouter une Indisponibilité</h5>
                            <a href="planning.php" class="btn-close" aria-label="Close"></a>
                        </div>
                        <div class="modal-body">
                            <form id="formAjouterIndispo" method="POST" action="planning.php">
                                <input type="hidden" name="formType" value="ajouterIndispo">

                                <div class="col-12">
                                    <label for="debutIndispo" class="form-label">Début (compris)</label>
                                    <input type="date" id="debutIndispo" name="debutIndispo" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label for="finIndispo" class="form-label">Fin (non-comprise)</label>
                                    <input type="date" id="finIndispo" name="finIndispo" class="form-control">
                                </div>
                                <?php if (isset($erreurs['vide'])) { ?>
                                        <div class="alert alert-danger"><?php echo $erreurs['vide']; ?></div>
                                <?php } elseif(isset($erreurs['superieur'])) {?>
                                        <div class="alert alert-danger"><?php echo $erreurs['superieur']; ?></div>
                                <?php } ?>
                                <button type="submit" class="btn btn-primary">Ajouter</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modale d'action (Modifier / Supprimer) -->
            <div class="modal fade <?php echo !empty($erreursModifier) ? 'show' : ''; ?>" id="actionModal" tabindex="-1" style ="<?php echo !empty($erreursModifier) ? 'display: block;' : 'display: none;'?>" aria-labelledby="actionModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="actionModalLabel">Modifier l'indisponibilité</h5>
                            <a href="planning.php" class="btn-close" aria-label="Close"></a>
                        </div>
                    <div class="modal-body">
                        <form id="formModifier" method="POST" action="planning.php">
                            <input type="hidden" name="formType" value="modifierIndispo">
                            <input type="hidden" id="idIndisponibilite" name="idIndisponibilite">

                            <div class="col-12">
                                <label for="debutIndispo" class="form-label">Début (compris)</label>
                                <input type="date" id="debut" name="debut" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="finIndispo" class="form-label">Fin (non-comprise)</label>
                                <input type="date" id="fin" name="fin" class="form-control">
                            </div>
                            <div class="modal-footer"> 
                                <?php if (isset($erreursModifier['vide'])) { ?>
                                        <div class="alert alert-danger"><?php echo $erreursModifier['vide']; ?></div>
                                <?php } elseif(isset($erreursModifier['superieur'])) {?>
                                        <div class="alert alert-danger"><?php echo $erreursModifier['superieur']; ?></div>
                                <?php } ?>
                                <button type="submit" title="Modifier l'indisponibilité" class="btn-action btn-modify btn-blue">Modifier</button>
                            </form> 
                            <form id="formSuppr" method="POST" action="planning.php">
                                <input type="hidden" name="supprimerIndisponibilite" id="hiddenDeleteInput">
                                <button type="submit" class="btn-action btn-delete" 
                                    id="deleteBtn"
                                    title="Supprimer l'indisponibilité"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette indisponibilité ?');">
                                    Supprimer
                                </button>
                            </form> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php require("../ressources/footer.php");?> 

    <!-- FullCalendar JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    
    <?php require("../ressources/planningJS.php");?> 
</body>
</html>