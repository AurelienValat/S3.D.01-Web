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