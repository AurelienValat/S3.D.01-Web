<?php 
    session_start();
    require ('../bdd/fonctions.php');
    verifSession(); // Vérifie si une session valide existe

    $estAdmin = isset($_SESSION['est_admin']) && $_SESSION['est_admin'] == 1;
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
                <button class="btn-blue btn-action">Exporter les données</button>
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

