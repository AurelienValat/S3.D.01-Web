<?php 
session_start();

require ('./bdd/connecterBD.php');
require ('./bdd/fonctions.php');

// Si l'utilisateur est déjà connecté, redirige vers accueil.php
if (isset($_SESSION['id'])) {
    header('Location: pages/accueil.php');
    exit;
}

// Objet de connexion à la BD
$pdo = initierConnexion();
if ($pdo == FALSE) {
    header("Location: pages/erreurs/erreurBD.php");
}

$login = isset($_POST['login']) ? $_POST['login'] : '';
$password = isset($_POST['mdp']) ? $_POST['mdp'] : '';

if (!empty($login) && !empty($password)) {
    // Authentification utilisateur
    $user = verifLoginMDP($pdo, $login, $password);
    if ($user) {
        $_SESSION['id'] = $user['id_employe'];
        $_SESSION['login'] = $user['nom_utilisateur'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['est_admin'] = $user['est_admin'];
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
		<link rel="stylesheet" href="./css/style.css"/>
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
            <div class="login-container">
                <form action="" method="post" class="login-form">

                        <h2>Connexion</h2><br>

                    <div class="form-group ">
                        <label>Identifiant</label><br>
                        <input type="text" name="login" value ="<?php echo reafficherSaisie('login') ?>" required>
                    </div><br>
                    <div class="form-group ">
                        <label>Mot de passe</label><br>
                        <input type="password" name="mdp" required>
                    </div><br>
                    <?php 
                        if ($erreur) {
                             echo '<div class="alert alert-danger ">Identifiant ou mot de passe incorrect.</div>';
                        }
                    ?>
                        <button type="submit" class="btn btn-primary">Se connecter</button>

                </form>
            </div>
        </div>
    </body>
</html>