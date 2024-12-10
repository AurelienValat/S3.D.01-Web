<?php 
require './bdd/connecterBD.php';

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
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">	
		<link rel="stylesheet" href="./css/index.css"/>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<title>MUSEOFLOW - CONNEXION</title>
	</head>
    
	<body class="fond">
		<div class="container">
            <!--Création de la première ligne -->
			<div class="row case">
				<div class="col-12 text-center">
					<h1 class="caveat">MUSEOFLOW</h1>
				</div>
            </div>

            <!-- Formulaire de login -->
            <div id="loginContainer" class="login-container">
                <form id="loginForm" class="login-form">
                    <h2>Connexion</h2><br>
                    <div class="form-group">
                        <label>Identifiant</label><br>
                        <input type="text" name="login" required>
                    </div><br>
                    <div class="form-group">
                        <label>Mot de passe</label><br>
                        <input type="password" name="password" required>
                    </div><br>
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </form>
            </div>
        </div>
    </body>
</html>
