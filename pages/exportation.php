<?php 
    session_start();
    require ('../bdd/fonctions.php');
    require ('../bdd/connecterBD.php');
    require ('../bdd/requetes.php');
    verifSession(); // Vérifie si une session valide existe

    $estAdmin = isset($_SESSION['est_admin']) && $_SESSION['est_admin'] == 1;

    $pdo = initierConnexion();
    if ($pdo == FALSE) {
        header("Location: pages/erreurs/erreurBD.php");
    }

    //Pour l'exportation
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $table = $_POST['table']; // Table sélectionnée dans le formulaire
    

        // Récupération depuis la BD
        $rows = exportationTable($pdo, $table);
        if ($rows == false) {
            // Le nom de la table est invalide
            exit;
        }

        $dateDuJour = date('d_m_y');

        $nomFichier = "{$table}s {$dateDuJour}.csv"; //Créer le nom du fichier
        
        // Indique que le contenu renvoyé est un fichier CSV
        header('Content-Type: text/csv');

        // Force le téléchargement du fichier avec le nom défini dans $nomFichier
        header('Content-Disposition: attachment; filename="' . $nomFichier . '"');
    
        $output = fopen('php://output', 'w');
        if ($rows) {
            fputcsv($output, array_keys($rows[0])); // Écrit les en-têtes des colonnes

            // Parcourt chaque ligne et l'écrit dans le fichier CSV
            foreach ($rows as $row) {
                // Il ne faut pas que les dates des indisponibilités soitent encadrés par des guillemets, 
                // ce qui se produit en utilisant fputcsv
                if ($table === 'conferencier') {
                    fputs($output, implode(';', $row)."\n");
                } else {
                    fputcsv($output, $row, ";");
                }
            }
        }
        
        fclose($output);
        exit;
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
                    Nous vous conseillons de sauvegarder vos fichiers une fois dans un même dossier prévu à cet effet.
                </p>
                <form action="exportation.php" method="POST">
                    <button type="submit" name="table" value="employe" class="btn-blue btn-action">Exporter employés</button>
                
                    <button type="submit" name="table" value="exposition" class="btn-blue btn-action">Exporter expositions</button>
               
                    <button type="submit" name="table" value="visite" class="btn-blue btn-action">Exporter visites</button>
               
                    <button type="submit" name="table" value="conferencier" class="btn-blue btn-action">Exporter conferenciers</button>
                </form>


            </div>
        </div>

        <br><br><br><br><br><br>

        <?php require("../ressources/footer.php");?>
    </body>
</html>

