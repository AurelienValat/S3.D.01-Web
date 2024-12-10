<?php 
    require '../../bdd/connecterBD.php';
    
    // Objet de connexion à la BD
    $pdo = initierConnexion();
    if ($pdo == TRUE) {
        // On détruit l'objet PDO pour ne pas surcharger si on appelle en boucle cette page
        $pdo = null;
        header("Location: ../../index.php");
    }
?>

<!DOCTYPE HTML>
<html lang="fr">
<head>
<link rel="stylesheet"
    href="../../CSS/bootstrap-5.3.2-dist/css/bootstrap.css">
<link rel="stylesheet"
    href="../../CSS/fontawesome-free-6.5.1-web/css/all.min.css">
    
<!-- Le CSS de la page -->
<link rel="stylesheet" href="../../CSS/style.css">

<meta charset="UTF-8">
<title>MEDILOG - Erreur</title>
</head>
<body>

    <!-- TODO mettre des IDs pour récupérer les informations -->

    <!-- Containeur principal de la page -->
    <div class="container">

        <!-- Entête de la page -->
        <div class="row cadre">

            <div class="col-md-3 col-sm-12 d-md-block d-sm-block d-none">
                <img class="logo_principal"
                    src="../../img/medicaments2.jpg">
            </div>

            <div class="col-md-9 col-sm-12 col-12">
                <h1>
                    <span class="fas fa-user-md"></span><br>APPLICATION<br>
                    MEDILOG
                </h1>
            </div>

            <div class="col-md-12 d-md-block d-sm-none d-none">

                <h2>
                    <span class="fas fa-id-card"></span> Création d'une
                    ordonnance <span class="fas fa-id-card"></span>
                </h2>
                
            </div>

        </div>
        
        <!-- Section recherche -->
        <div class="row cadre">
            <br>
            <p>---</p>
            <h1>Erreur de connexion à la base de données. 
            <br>Contactez votre administrateur système.</h1>
            <p>---</p>
            <br>
        </div>

        <!-- Intérieur du menu + description -->
        <div class="menu cadre row">

            <div class="col-12">
                <span class="fas fa-sitemap"></span> Menu :
            </div>

            <!-- Sous menus -->

            <div class="sub_menu col-md-3 col-12">
                <span class="fas fa-home"></span> <a
                    href="../../index.html">Accueil</a>
            </div>

            <div class="sub_menu col-md-3 col-12">
                <span class="fas fa-search"></span> <a
                    href="../recherche.html">Recherche d'un
                    médicament</a>
            </div>

            <div class="sub_menu col-md-3 col-12">
                <span class="fas fa-id-card"></span> <a
                    href="../ordonnance.html">Création ordonnance</a>
            </div>

            <!-- Crédit -->
            <div class="credit col-md-3 col-12">
                <p>
                    <span class="fa fa-spinner fa-spin"></span> Réalisé
                    par
                </p>
                <img class="logo_iut" src="../../img/LogoIut.png">
            </div>

        </div>

    </div>
</body>
</html>