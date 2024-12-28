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
        $table = $_POST['table']; // Table sélectionnée
        $tablesValides = ['employe', 'exposition', 'visite', 'conferencier']; 
    
        //Si la table n'est pas valide
        if (!in_array($table, $tablesValides)) {
            die('Table non valide.');
        }
    
        $nomFichier = "{$table}s.csv";
        $stmt = $pdo->query("SELECT * FROM {$table}");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $nomFichier . '"');
    
        $output = fopen('php://output', 'w');
        if ($rows) {
            fputcsv($output, array_keys($rows[0]));
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
                </form>
                <form action="exportation.php" method="POST">
                    <button type="submit" name="table" value="exposition" class="btn-blue btn-action">Exporter exposition</button>
                </form>
                <form action="exportation.php" method="POST">
                    <button type="submit" name="table" value="visite" class="btn-blue btn-action">Exporter visite</button>
                </form>
                <form action="exportation.php" method="POST">
                    <button type="submit" name="table" value="conferencier" class="btn-blue btn-action">Exporter conferencier</button>
                </form>


            </div>
        </div>

        <br><br><br><br><br><br>

        <footer>

            <div>
                <h5>Contacter le support</h5>
                <a href="tel:0123456789">01.23.45.67.89</a><br>
                <a href="mailto:supportclient@contact.com">supportclient@contact.com</a>
                <p>12 rue de l'invention, 12000 Rodez</p>
            </div>

            <div>
                <p>LOUBIERE, POUPIN, SEHIL, VALAT © 2024</p>
            </div>

        </footer>
    </body>
</html>

