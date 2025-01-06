<?php 
    session_start();
    require ('../bdd/fonctions.php');
    require ('../bdd/connecterBD.php');
    verifSession(); // Vérifie si une session valide existe

    $estAdmin = isset($_SESSION['est_admin']) && $_SESSION['est_admin'] == 1;

    $pdo = initierConnexion();
    if ($pdo == FALSE) {
        header("Location: pages/erreurs/erreurBD.php");
    }

    //Pour l'exportation
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $table = $_POST['table']; // Table sélectionnée dans le formulaire
        $tablesValides = ['employe', 'exposition', 'visite', 'conferencier']; 
    
        // Déterminer les colonnes à sélectionner en fonction de la table
        if ($table === 'employe') {
            $colonnes = 'nom, prenom, no_tel';
        } else {
            $colonnes = '*'; // Sélectionne tout pour les autres tables
        }

        $nomFichier = "{$table}s.csv"; //Créer le nom du fichier
        //Requete pour selectionner les données à exporter
        $stmt = $pdo->query("SELECT {$colonnes} FROM {$table}"); 
        $rows = $stmt->fetchAll();
    
        // Indique que le contenu renvoyé est un fichier CSV
        header('Content-Type: text/csv');

        // Force le téléchargement du fichier avec le nom défini dans $nomFichier
        header('Content-Disposition: attachment; filename="' . $nomFichier . '"');
    
        $output = fopen('php://output', 'w');
        if ($rows) {
            fputcsv($output, array_keys($rows[0])); // Écrit les en-têtes des colonnes

            // Parcourt chaque ligne et l'écrit dans le fichier CSV
            foreach ($rows as $row) {
                fputcsv($output, $row);
            }
        }
        fclose($output);
        exit;
    }
    
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">  
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script>   
    <title>MUSEOFLOW - Exportation des fichiers</title>
</head>
<body>
    
    <?php require("../ressources/navBar.php");?>

        <div class="container content ">
            <div class="container-blanc justify-content-center col-12">
                <p>
                    Pour éviter tout conflits dans les données nous recommandons d'exporter la totalité des données en même temps.
                </p>
                <p>
                    Attention, nous vous conseillons de mettre vos fichiers une fois exportés dans un dossier prévu à cet effet.
                </p>
                <form action="exportation.php" method="POST">
                    <button type="submit" name="table" value="employe" class="btn-blue btn-action">Exporter employe</button>
                
                    <button type="submit" name="table" value="exposition" class="btn-blue btn-action">Exporter exposition</button>
               
                    <button type="submit" name="table" value="visite" class="btn-blue btn-action">Exporter visite</button>
               
                    <button type="submit" name="table" value="conferencier" class="btn-blue btn-action">Exporter conferencier</button>
                </form>


            </div>
        </div>

        <br><br><br><br><br><br>

        <?php require("../ressources/footer.php");?>
    </body>
</html>

