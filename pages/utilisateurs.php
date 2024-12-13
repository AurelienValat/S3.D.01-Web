<?php 
session_start();
require ('../bdd/fonctions.php');
require ('../bdd/connecterBD.php');
require ('../bdd/requetes.php');


verifSession(); // Vérifie si une session valide existe

if (!isset($_SESSION['est_admin']) || $_SESSION['est_admin'] != 1) {
    // Rediriger l'utilisateur vers une autre page s'il n'est pas admin
    header('Location: accueil.php');
    exit();
}
$pdo = initierConnexion();
if ($pdo == FALSE) {
    header("Location: pages/erreurs/erreurBD.php");
}

if (isset($_POST['supprimerEmploye'])) {
    supprimerEmploye($pdo);
}


// Initialisation des erreurs
$errors = [];
$isUserCreated = false;
$showModal = false;

try {
    // Initialisation des variables de formulaire
    $username = isset($_POST['username']) ? trim($_POST['username']) : ""; 
    $password = isset($_POST['password']) ? trim($_POST['password']) : "";
    $confirmPassword = isset($_POST['confirmPassword']) ? trim($_POST['confirmPassword']) : "";
    $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : "";
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : "";
    $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : "";
    
    // Validation des champs
    if (empty($username) || strlen($username) < 5 || strlen($username) > 20) {
        $errors['username'] = 'Nom d\'utilisateur invalide (5-20 caractères).';
    }
    if (empty($password) || strlen($password) > 35) {
        $errors['password'] = 'Mot de passe trop long (max 35 caractères).';
    }
    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = 'Les mots de passe ne correspondent pas.';
    }
    if (empty($prenom) || strlen($prenom) > 35) {
        $errors['prenom'] = 'Le prénom est requis et ne doit pas dépasser 35 caractères.';
    }
    if (empty($nom) || strlen($nom) > 35) {
        $errors['nom'] = 'Le nom est requis et ne doit pas dépasser 35 caractères.';
    }
    if (empty($telephone) || !preg_match("/^[0-9]{4}$/", $telephone)) {
        $errors['telephone'] = 'Numéro de téléphone invalide. Il doit contenir 4 chiffre.';
    }

    // Si aucun champ n'a d'erreur, procéder à l'insertion
    if (empty($errors)) {
        $pdo = initierConnexion();
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO employe (nom_utilisateur, nom, prenom, no_tel, mot_de_passe, est_admin) 
        VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $nom, $prenom, $telephone, $passwordHash, 0]);

        $isUserCreated = true; // Indique qu'un utilisateur a été créé
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>".$e->getMessage()."</p>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script>   
    <title>MUSEOFLOW - Gestion des Utilisateurs</title>
</head>
<body class="fond">
    <nav class="navbar">
        <div class="logo">
            <a href="accueil.php"><img class="logo-img" src="../ressources/images/logo.png" alt="Logo MuseoFlow"></a>
            Intranet du Musée
        </div>
        <div class="main-menu">
            <a href="utilisateurs.php" class="deco"><div class="menu-item">Utilisateurs</div></a>
            <a href="expositions.php" class="deco"><div class="menu-item">Expositions</div></a>
            <a href="conferenciers.php" class="deco"><div class="menu-item">Conférenciers</div></a>
            <a href="visites.php" class="deco"><div class="menu-item">Visites</div> </a>
            <a href="exportation.php" class="deco"><div class="menu-item">Exportation</div></a>
            <!-- Menu déroulant -->
            <div class="dropdown">
                <div class="menu-item"><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($_SESSION['prenom']); ?> <i class="fa-solid fa-angle-down"></i></i></div>
                <div class="dropdown-menu">
                    <a href="deconnexion.php" class="btn-red">Se déconnecter</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container content">
    <div class="container-blanc">
        <h1 class="text-center">Gestion des Utilisateurs</h1>
        <div class="d-flex justify-content-between align-items-center">
        <button class="btn-action btn-modify btn-blue" data-bs-toggle="modal" data-bs-target="#modalAjouterUtilisateur" id="modalAjouterUtilisateurLabel">Ajouter un utilisateur</button>
            <button class="btn btn-light d-flex align-items-center gap-2">
            <i class="fa-solid fa-filter"></i>Filtres
            </button>
        </div>
        <div class="table">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Identifiant</th>
                        <th>Nom</th>
                        <th>Prenom</th>
                        <th>Numéro de téléphone</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <?php 
                        // Récupération de la liste des employés/utilisateurs depuis la BD
                        $utilisateurs = getUtilisateurs($pdo);
                        $totalUtilisateurs = 0;
                        $dernierUtilisateur = "";
                        
                        while($ligne = $utilisateurs->fetch()) {
                                echo "<tr>";
                                    echo "<td>".$ligne['identifiant']."</td>";
                                    echo "<td>".$ligne['nom']."</td>";
                                    echo "<td>".$ligne['prenom']."</td>";
                                    echo "<td>".$ligne['no_tel']."</td>";
                                    if ($ligne['est_admin'] == 1){
                                       echo "<td>". 'Administrateur' ."</td>";
                                    } else {
                                        echo "<td>". 'Employé' ."</td>";
                                    }
                                    echo "<td>";
                                        echo "<button class='btn-action btn-modify btn-blue'>Modifier</button>";
                                        if ($ligne['est_admin'] == 0){
                                            echo "<button class='btn-action btn-delete'>Supprimer</button>";
                                        }
                                    echo "</td>";
                                echo "</tr>";
                                echo "";
                                $totalUtilisateurs++ ;
                                $dernierUtilisateur = $ligne['prenom'];
                            }
                    ?>
                </tbody>
            </table>
            <?php 
            echo $totalUtilisateurs . " utilisateur(s) trouvé(s)";
            // Si seule compte admin par défaut existe
            if (strcmp($dernierUtilisateur, "par défaut") == 0 && $totalUtilisateurs == 1) {
                echo "<div class='text-center'>Aucun employé n'est enregistré.</div>";
            }?>
        </div>
    </div>
    <!-- Modale Ajouter Utilisateur -->
    <div class="modal" id="modalAjouterUtilisateur" tabindex="-1" aria-labelledby="modalAjouterUtilisateurLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAjouterUtilisateurLabel">Ajouter un utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAjouterUtilisateur" method="POST" action="utilisateurs.php">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nom d'utilisateur</label>
                            <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>">
                            <?php if (isset($errors['username'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['username']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password">
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirmation du Mot de passe</label>
                            <input type="password" class="form-control <?php echo isset($errors['confirmPassword']) ? 'is-invalid' : ''; ?>" id="confirmPassword" name="confirmPassword">
                            <?php if (isset($errors['confirmPassword'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['confirmPassword']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control <?php echo isset($errors['prenom']) ? 'is-invalid' : ''; ?>" id="prenom" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>">
                            <?php if (isset($errors['prenom'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['prenom']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control <?php echo isset($errors['nom']) ? 'is-invalid' : ''; ?>" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>">
                            <?php if (isset($errors['nom'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['nom']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Numéro de téléphone</label>
                            <input type="tel" class="form-control <?php echo isset($errors['telephone']) ? 'is-invalid' : ''; ?>" id="telephone" name="telephone" value="<?php echo htmlspecialchars($telephone); ?>">
                            <?php if (isset($errors['telephone'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['telephone']; ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modale de Confirmation -->
    <div class="modal <?php echo $isUserCreated ? 'show' : ''; ?>" 
        id="modalConfirmation" 
        tabindex="-1" 
        aria-labelledby="modalConfirmationLabel" 
        aria-hidden="<?php echo $isUserCreated ? 'false' : 'true'; ?>" 
        style="<?php echo $isUserCreated ? 'display: block;' : 'display: none;'; ?>">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmationLabel">Succès</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Employé créé avec succès.</p>
                </div>
                <div class="modal-footer">
                    <a href="utilisateurs.php" class="btn btn-secondary">Fermer</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


