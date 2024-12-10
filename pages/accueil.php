<?php 
require '../bdd/connecterBD.php';

// Objet de connexion à la BD
$pdo = initierConnexion();
if ($pdo == FALSE) {
    header("Location: ./erreurs/erreurBD.php");
}
// Pour réafficher la saisie utilisateur pour les champs texte
function reafficherSaisie($nomChamp) {
    if (isset($_POST[$nomChamp]) && trim($_POST[$nomChamp]) !== '') {
        return $_POST[$nomChamp];
    } else {
        return '';
    }
}
// Pour réafficher la saisie utilisateur pour listes déroulantes
function reafficherSaisieOption($valeurOption, $nomChamp) {
    if (isset($_POST[$nomChamp]) && $_POST[$nomChamp] === $valeurOption) {
        return 'selected';
    } else {
        return '';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">  
        <link href="../css/navBar.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <title>MUSEOFLOW - Acceuil</title>
    </head>
    
    <body class="fond">
        <nav class="navbar">
            <div class="logo">
                <a href="..\index.php"><img class="logo-img" src="../ressources/images/logo.png" alt="Logo MuseoFlow"></a>
                Intranet du Musée
            </div>
            <div class="main-menu">
                <div class="menu-item">Utilisateurs</div>
                <div class="menu-item">Expositions</div>
                <div class="menu-item">Conférenciers</div>
                <div class="menu-item">Visites</div>
                <div class="menu-item">Exportation</div>
                <div class="menu-item">Déconnexion</div>
            </div>
        </nav>
</html>
