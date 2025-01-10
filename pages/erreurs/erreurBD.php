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
    <link rel="icon" type="image/png" href="../../ressources/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="../../ressources/favicon/favicon.svg" />
    <link rel="shortcut icon" href="../../ressources/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="../../ressources/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MuseoFlow" />
    <link  href="../../css/style.css" rel="stylesheet"/>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">
    
    <meta charset="UTF-8">
    <title>MuseoFlow - Erreur</title>
</head>
<body>
    <!-- Containeur principal de la page -->
    <div class="container login-container">

        <div class="row">
            <!-- Entête de la page -->
            <div class="col-12">
                <h1 class="blanc">Erreur de connexion à la base de donnée. Veuillez réessayer plus tard.</h1>
            </div>
        </div>
    </div>
</body>
</html>