<?php
session_start();
require ('../../bdd/fonctions.php');

verifSession(); // Vérifie si une session valide existe

$estAdmin = isset($_SESSION['est_admin']) && $_SESSION['est_admin'] == 1;

if (!isset($_SESSION['cheminDernierePage']) 
    || !isset($_SESSION['donneeEnErreur']) 
    || trim($_SESSION['donneeEnErreur']) == "" 
    || trim($_SESSION['cheminDernierePage']) == "") {
        header("Location: ../accueil.php");
    }

?>
<!DOCTYPE HTML>
<html lang="fr">
<head>
    <link  href="../../css/style.css" rel="stylesheet"/>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script> 
    
    <meta charset="UTF-8">
    <title>MuseoFlow - Erreur</title>
</head>
<body>
<?php require("../../ressources/navBar.php");?>
    <!-- Containeur principal de la page -->
    <div class="container login-container">

        <div class="row">
            <!-- Entête de la page -->
            <div class="col-12">
                <h1 class="blanc">Impossible de supprimer la donnée demandée : <?php echo $_SESSION['donneeEnErreur']?></h1>
                <h2 class="blanc">Une visite planifiée utilise cette donnée, vous devez la supprimer d'abord.</h2>
                <form action="<?php echo $_SESSION['cheminDernierePage'];?>">
                    <button id='btn_retour' class='btn-action btn-modify btn-blue'><span class='fa-classic fa-solid fa-arrow-left-long fa-fw'></span>Retour</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>