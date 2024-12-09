<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">	
		<link rel="stylesheet" href="css\index.css"/>
		<link href="ressources\bootstrap\bootstrap-5.3.2-dist\css\bootstrap.css" rel="stylesheet">
		<link href="ressources\fontawesome\fontawesome-free-6.5.1-web\css\all.css" rel="stylesheet">
		<title>MUSEOFLOW -CONNEXION</title>
	</head>
	<body>
		<div class="container">

            <!--Création de la premiere ligne -->
			<div class="row case ">
				<div class="col-12">
					<h1>MUSEOFLOW</h1>
				</div>
            </div>

            <!-- Formulaire de login -->
            <div id="loginContainer" class="login-container">
                <form id="loginForm" class="login-form">
                    <h2>Connexion</h2>
                    <div class="form-group">
                    <label>Identifiant</label>
                    <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" required>
                    </div>
                    <button type="submit" class="button">Se connecter</button>
                </form>
            </div>

        </div>
    </body>
</html>
