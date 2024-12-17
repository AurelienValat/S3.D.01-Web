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
        $userIdToDelete = intval($_POST['supprimerVisite']); // Sécuriser la donnée
        try {
            supprimerLigne($pdo, $userIdToDelete, "Visite");
        } catch (PDOException) {
            header("Location: erreurs/erreurBD.php");
        }
    }
    

    $exposRecup = recupExpositions($pdo);
    $confsRecup = recupConferenciers($pdo);

    $ToutOK = true;
    $erreurs = [];

    if (!isset($_POST['exposition']) || $_POST['exposition'] == "zero") {
        $erreurs['exposition'] = "Veuillez sélectionner une exposition.";
        $ToutOK = false;
    } else {
        $exposition = htmlspecialchars($_POST['exposition']);
    }

    // Vérification du champ "conferencier"
    if (!isset($_POST['conferencier']) || $_POST['conferencier'] == "zero") {
        $erreurs['conferencier'] = "Veuillez sélectionner un conférencier.";
        $ToutOK = false;
    } else {
        $conferencier = htmlspecialchars($_POST['conferencier']);
    }

    // Vérification de la date
    if (!isset($_POST['date']) || $_POST['date'] == "") {
        $erreurs['date'] = "Veuillez entrer une date.";
        $ToutOK = false;
    } else {
        $date = htmlspecialchars($_POST['date']);
    }

    // Vérification de l'heure
    if (!isset($_POST['heure']) || $_POST['heure'] == "") {
        $erreurs['heure'] = "Veuillez entrer une heure.";
        $ToutOK = false;
    } else {
        $heure = htmlspecialchars($_POST['heure']);
    }

    // Vérification du client
    if (!isset($_POST['client']) || trim($_POST['client']) == "") {
        $erreurs['client'] = "Le nom du client est obligatoire.";
        $ToutOK = false;
    } else {
        $client = htmlspecialchars($_POST['client']);
    }

    // Vérification du téléphone
    if (!isset($_POST['telephone']) || !preg_match('/^0[1-9]([-. ]?[0-9]{2}){4}$/', $_POST['telephone'])) {
        $erreurs['telephone'] = "Le numéro de téléphone n'est pas valide.";
        $ToutOK = false;
    } else {
        $telephone = htmlspecialchars($_POST['telephone']);
    }

     // Si tout est OK, on insère dans la base
    if ($ToutOK) {
        creerExpositions($pdo, $exposition, $conferencier, $_SESSION['id'], $heure, $date, $client, $telephone);
    } 

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">  
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script>   
    <title>MUSEOFLOW - Gestion des Visites</title>
</head>
<body class="fond">
   
    <?php require("../ressources/navBar.php");?>

    <div class="container content col-12">
        <div class="container-blanc">
            <h1 class="text-center">Gestion des Visites</h1>
            <!-- Utilisation de Flexbox pour mettre sur la même ligne les boutons -->
            <div class="d-flexd-flex gap-2 mt-3">
                <!-- Menu Ajouter/Réserver -->
                <details id="reserverDetails">
                    <summary class="btn btn-primary btn-sm" style="cursor: pointer;">Ajouter/Réserver une visite</summary>
                    <form action="visites.php" method="POST" class="p-3 border rounded bg-light mt-3">
                        <div >
                            <label for="exposition" class="form-label">Nom de l'exposition :</label>
                            <select name="exposition" id="exposition" required>
                                <option value="zero">--Veuillez choisir l'exposition--</option>
                                <?php 
                                    // Parcourir les expositions et afficher chaque option
                                    foreach ($exposRecup as $exposition) {
                                        echo '<option value="' . htmlspecialchars($exposition['id_exposition']) . '">'
                                            . htmlspecialchars($exposition['intitule']) . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div >
                            <label for="conferencier" class="form-label">Séléctionner le conférencier :</label>
                            <select name="conferencier" id="conferencier" required>
                                <option value="zero">--Veuillez choisir le conférencier--</option>
                                <?php 
                                    // Parcourir les conférenciers et afficher chaque option
                                    foreach ($confsRecup as $conferencier) {
                                        echo '<option value="' . htmlspecialchars($conferencier['id_conferencier']) . '">'
                                            . htmlspecialchars($conferencier['prenom'] ) . '</option>';
                                    }
                                ?>
                            </select>          
                        </div>
                        <div >
                            <label for="date" class="form-label">Date de la visite:</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                            <div class="text-danger"><?= $erreurs['date'] ?? "" ?></div>
                        </div>
                        <div >
                            <label for="heure" class="form-label">Heure de début de la visite:</label>
                            <input type="time" class="form-control" id="heure" name="heure" required>
                        </div>
                        <div >
                            <label for="client" class="form-label">Nom du client qui a réservé:</label>
                            <input type="text" class="form-control" id="client" name="client" required>
                        </div>
                        <div>
                            <label for="telephone" class="form-label">Numéro de teléphone du client:</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Valider</button>
                    </form>
                </details>

                <!-- Menu Filtres -->
                <details id="filtreDetails">
                    <summary class="btn btn-secondary btn-sm" style="cursor: pointer;">Filtres</summary>
                    <div class="p-3 border rounded bg-light mt-3">
                        
                    </div>
                </details>
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const reserverDetails = document.querySelector("#reserverDetails");
        const filtreDetails = document.querySelector("#filtreDetails");

        reserverDetails.addEventListener("toggle", function () {
            if (reserverDetails.open) {
                // Cache le bouton "Filtres" si "Ajouter/Réserver" est ouvert
                filtreDetails.classList.add("hidden");
            } else {
                // Affiche le bouton "Filtres" sinon
                filtreDetails.classList.remove("hidden");
            }
        });
    });
</script>

</body>
</html>