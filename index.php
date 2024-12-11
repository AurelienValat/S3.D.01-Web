<?php 
require ('./bdd/connecterBD.php');
require ('./bdd/fonctions.php');


// Objet de connexion à la BD
$pdo = initierConnexion();
if ($pdo == FALSE) {
    header("Location: ./erreurs/erreurBD.php");
}

$login = isset($_POST['login']) ? $_POST['login'] : '';
$password = isset($_POST['mdp']) ? $_POST['mdp'] : '';

if (!empty($login) && !empty($password)) {
    // Authentification utilisateur
    $user = verifLoginMDP($pdo, $login, $password);
    if ($user) {
        // Démarrer la session pour l'utilisateur authentifié
        startSession($user);
        header('Location: pages/accueil.php');
        exit;
    } else {
        $erreur = true;
    }
} else {
    $erreur = false;
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
    
	<body>
		<div class="container">
            <!--Création de la première ligne -->
			<div class="row">
				<div class="col-12 text-center">
					<h1 class="caveat">MUSEOFLOW</h1>
				</div>
            </div>

            <!-- Formulaire de login -->
            <div id="loginContainer" class="login-container">
                <form action="" method="post" id="loginForm" class="login-form">
                    <h2>Connexion</h2><br>

                    <div class="form-group">
                        <label>Identifiant</label><br>
                        <input type="text" name="login" value ="<?php echo reafficherSaisie('login') ?>" required>
                    </div><br>
                    <div class="form-group">
                        <label>Mot de passe</label><br>
                        <input type="password" name="mdp" required>

                    </div><br>
                    <?php 
                        if ($erreur) {
                             echo '<div class="alert alert-danger">Identifiant ou mot de passe incorrect.</div>';
                        }
                    ?>
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </form>
            </div>
        </div>
    </body>
</html>