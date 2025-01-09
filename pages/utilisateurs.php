<?php 
session_start();

require ('../bdd/fonctions.php');
require ('../bdd/connecterBD.php');
require ('../bdd/requetes.php');


verifSession(); // Vérifie si une session valide existe

$estAdmin = isset($_SESSION['est_admin']) && $_SESSION['est_admin'] == 1;

if (!isset($_SESSION['est_admin']) || $_SESSION['est_admin'] != 1) {
    // Rediriger l'utilisateur vers une autre page s'il n'est pas admin
    header('Location: accueil.php');
    exit();
}
$pdo = initierConnexion();
if ($pdo == FALSE) {
    header("Location: pages/erreurs/erreurBD.php");
}

// Vérification si une suppression est demandée
if (isset($_POST['supprimerEmploye']) && $_POST['supprimerEmploye'] != trim('')) {
    $userIdToDelete = intval($_POST['supprimerEmploye']); // Sécuriser la donnée
    try {
    supprimerLigne($pdo, $userIdToDelete, "Employe");
    } catch (PDOException) {
        $_SESSION['donneeEnErreur'] = 'utilisateur';
        $_SESSION['cheminDernierePage'] = '/S3.D.01-Web/pages/utilisateurs.php';
        header("Location: ./erreurs/impossibleDeTraiterVotreDemande.php");
    }
}


// Initialisation des erreurs
$erreurs = [];

$utilisateurCree = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['supprimerEmploye']) && !isset($_POST['idUtilisateur']) && !isset($_POST['demandeFiltrage'])) {
    try {
        // Initialisation des variables de formulaire
        $pseudo = isset($_POST['pseudo']) ? trim($_POST['pseudo']) : ""; 
        $motDePasse = isset($_POST['motDePasse']) ? trim($_POST['motDePasse']) : "";
        $confirmeMotDePasse = isset($_POST['confirmeMotDePasse']) ? trim($_POST['confirmeMotDePasse']) : "";
        $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : "";
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : "";
        $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : "";
        
        // Validation des champs
        if (($pseudo == "") || strlen($pseudo) < 2 || strlen($pseudo) > 20) {
            $erreurs['pseudo'] = 'Nom d\'utilisateur invalide (2-20 caractères).';
        }
        // TODO essayer de faire en sorte d'obliger d'avoir un mdp avec une majuscule 
        // un caractere spéciale et min 6 caracteres avec des points qui deviennent vert 
        // quand une des obligations est faite
        if (($motDePasse == "") || strlen($motDePasse) > 255) {
            $erreurs['motDePasse'] = 'Mot de passe trop long (max 255 caractères).';
        }
        if (($motDePasse == "") || strlen($motDePasse) < 5) {
            $erreurs['motDePasse'] = 'Mot de passe trop court (min 5 caractères).';
        }
        if ($motDePasse !== $confirmeMotDePasse) {
            $erreurs['confirmeMotDePasse'] = 'Les mots de passe ne correspondent pas.';
        }
        if (($prenom == "") || strlen($prenom) > 50) {
            $erreurs['prenom'] = 'Le prénom est requis et ne doit pas dépasser 50 caractères.';
        }
        if (($nom == "") || strlen($nom) > 50) {
            $erreurs['nom'] = 'Le nom est requis et ne doit pas dépasser 50 caractères.';
        }
        if (!preg_match("/^[0-9]{4}$/", $telephone) && $telephone != "") {
            $erreurs['telephone'] = 'Numéro de téléphone invalide. Il doit contenir 4 chiffre.';
        }

        // Si aucun champ n'a d'erreur, procéder à l'insertion
        if (empty($erreurs)) {
            if (verifierExistanceUtilisateur($pdo, $pseudo, $nom, $prenom)) {
                $erreurs['existance'] = 'Un utilisateur avec ce nom d\'utilisateur ou ce nom et prénom existe déjà.';
            } else {
                creerEmploye($pdo, $pseudo, $nom, $prenom, $telephone, $motDePasse);
                $utilisateurCree = true; // Indique qu'un utilisateur a été créé
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color:red;'>".$e->getMessage()."</p>";
    }
}  else {
    // Initialiser les variables de formulaire à des valeurs vides si pas de soumission
    $pseudo = $motDePasse = $confirmeMotDePasse = $prenom = $nom = $telephone = "";
}

//Pour la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idUtilisateur'])) {
    try {
        $idUtilisateur = intval($_POST['idUtilisateur']); // Identifiant unique de l'utilisateur
        $pseudo = isset($_POST['pseudoUtilisateur']) ? trim($_POST['pseudoUtilisateur']) : "";
        $prenom = isset($_POST['prenomUtilisateur']) ? trim($_POST['prenomUtilisateur']) : "";
        $nom = isset($_POST['nomUtilisateur']) ? trim($_POST['nomUtilisateur']) : "";
        $telephone = isset($_POST['telephoneUtilisateur']) ? trim($_POST['telephoneUtilisateur']) : "";
        $motDePasse = isset($_POST['motDePasseUtilisateur']) ? trim($_POST['motDePasseUtilisateur']) : "";
        $confirmeMotDePasse = isset($_POST['confirmeMotDePasseUtilisateur']) ? trim($_POST['confirmeMotDePasseUtilisateur']) : "";

        // Validation des champs
        $erreursModif = [];
        if ($pseudo === "" || strlen($pseudo) < 5 || strlen($pseudo) > 20) {
            $erreursModif['pseudo'] = 'Nom d\'utilisateur invalide (5-20 caractères).';
        }
        if (($prenom == "") || strlen($prenom) > 35) {
            $erreursModif['prenom'] = 'Le prénom est requis et ne doit pas dépasser 35 caractères.';
        }
        if (($nom == "") || strlen($nom) > 35) {
            $erreursModif['nom'] = 'Le nom est requis et ne doit pas dépasser 35 caractères.';
        }
        if (!preg_match("/^[0-9]{4}$/", $telephone) && $telephone != "") {
            $erreursModif['telephone'] = 'Numéro de téléphone invalide. Il doit contenir 4 chiffres.';
        }
        if ($motDePasse != "" && strlen($motDePasse) > 35) {
            $erreursModif['motDePasse'] = 'Mot de passe trop long (max 35 caractères).';
        }
        if ($motDePasse != "" && strlen($motDePasse) < 5) {
            $erreursModif['motDePasse'] = 'Mot de passe trop court (min 5 caractères).';
        }
        if ($motDePasse != "" && $motDePasse !== $confirmeMotDePasse) {
            $erreursModif['confirmeMotDePasse'] = 'Les mots de passe ne correspondent pas.';
        }

        // Si aucun champ n'a d'erreur, mettre à jour la BD
        if (empty($erreursModif)) {
            if (verifierExistanceUtilisateurModif($pdo, $pseudo, $nom, $prenom, $idUtilisateur)) {
                $erreursModif['existance'] = 'Un utilisateur avec ce nom d\'utilisateur ou ce nom et prénom existe déjà.';
            } else {
                // Construire les données pour la mise à jour
                $donneesAModif = [
                    'nom_utilisateur' => $pseudo,
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'no_tel' => $telephone,
                ];

                // Ajouter le mot de passe uniquement s'il est changé
                if (!empty($motDePasse)) {
                    $donneesAModif['mot_de_passe'] = hash('sha256', $motDePasse);
                }

                // Mettre à jour l'utilisateur dans la BD
                modifUtilisateur($pdo, $idUtilisateur, $donneesAModif);

                // Affichage du message de confirmation
                // désactivé car s'affiche avant le doctype
                //echo "<script>alert('Utilisateur modifié avec succès.')</script>";
            }
            
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'>Une erreur est survenue : " . $e->getMessage() . "</p>";
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" >
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script>
    <script src="../js/utilisateurs.js" type="text/javascript"></script>
    <link href="../css/style.css" rel="stylesheet">

    <title>MUSEOFLOW - Gestion des Utilisateurs</title>
</head>
<body class="fond">
    
    <?php 
    // Pour afficher les options de filtrages spécifiques aux employés
    $_SESSION['filtreAApliquer'] = 'utilisateurs';
    require("../ressources/navBar.php");
    require("../ressources/filtres.php");

    ?>

    <div class="container content">
    <div class="container-blanc">
        <h1 class="text-center">Gestion des Utilisateurs</h1>
        <div class="d-flex justify-content-between align-items-center">
        <button class="btn-action btn-modify btn-blue" onclick="resetFormulaire()" data-bs-toggle="modal" data-bs-target="#modalAjouterUtilisateur" id="modalAjouterUtilisateurLabel" title="Ajouter un utilisateur"><i class="fa-solid fa-user-plus"></i></button>
            <button
                class="btn btn-secondary d-flex align-items-center gap-2 filtrage"
                data-bs-toggle="modal" data-bs-target="#modalFiltrage" >
                <i class="fa-solid fa-filter" ></i>Filtres
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
                     // Traitements si un filtrage est demandé
                     if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['demandeFiltrage']) && $_POST['demandeFiltrage'] === '1') {
                        $nomRecherche = isset($_POST['rechercheNom']) ? trim($_POST['rechercheNom']) : '';
                        $prenomRecherche = isset($_POST['recherchePrenom']) ? trim($_POST['recherchePrenom']) : '';
                    
                        $utilisateurs = rechercheUtilisateurs($pdo, $nomRecherche, $prenomRecherche);

                        echo '<a href="utilisateurs.php"><button class="btn-action btn-modify btn-blue"><span class="fa fa-refresh"></span> Effacer les filtres</button></a><br>';
                        echo '<h2>Filtres appliqués :</h2>';
                        
                        // Affichage des filtres appliqués
                        if (!empty($_POST['rechercheNom'])) {
                            echo "Nom : '" . htmlspecialchars($_POST['rechercheNom']) . "' :<br>";
                        }
                        
                        if (!empty($_POST['recherchePrenom'])) {
                            echo "Prénom : '" . htmlspecialchars($_POST['recherchePrenom']) . "' :<br>";
                        }

                        
                    } else {
                        // Récupération de la liste des employés/utilisateurs depuis la BD
                        $utilisateurs = getUtilisateurs($pdo);
                    }
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
                                    echo "<button 
                                        class='btn-action btn-modify btn-blue' 
                                        data-bs-toggle='modal'
                                        data-bs-target='#modalMofifierUtilisateur' 
                                        title='Modifier l&#39;utilisateur'
                                        onclick='remplirFormulaire(
                                            " . intval($ligne['id_employe']) . ", 
                                            \"" . addslashes($ligne['identifiant']) . "\",
                                            \"" . addslashes($ligne['prenom']) . "\",
                                            \"" . addslashes($ligne['nom']) . "\",
                                            \"" . addslashes($ligne['no_tel']) . "\"
                                        )'>
                                        <i class='fa-solid fa-pencil'></i>
                                    </button>";
                                        
                                if ($ligne['est_admin'] == 0){
                                            ?>
                                            <form method="POST" action="utilisateurs.php" style="display:inline;">
                                                <input type="hidden" name="supprimerEmploye" value="<?php echo $ligne['id_employe']; ?>">
                                                <button type="submit" class="btn-action btn-delete" title="Supprimer l'exposition"onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');"><i class="fa-solid fa-trash"></i></button>
                                                </form>
                                            <?php
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
            // Si seul le compte admin par défaut existe
            if (strcmp($dernierUtilisateur, "par défaut") == 0 && $totalUtilisateurs == 1) {
                echo "<div class='text-center'>Aucun employé n'est enregistré.</div>";
            }?>
        </div>
    </div>

    <!-- Modale Ajouter Utilisateur -->
    <div class="modal fade <?php echo !empty($erreurs) ? 'show' : ''; ?>" 
        id="modalAjouterUtilisateur" 
        style="<?php echo !empty($erreurs) ? 'display: block;' : 'display: none;'; ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAjouterUtilisateurLabel">Ajouter un utilisateur</h5>
                    <a href="utilisateurs.php" class="btn-close" aria-label="Close"></a>
                </div>
                <div class="modal-body">
                    <form id="formAjouterUtilisateur" method="POST" action="utilisateurs.php">
                        <div class="mb-3">
                            <label for="pseudo" class="form-label">Identifiant</label>
                            <input type="text" 
                                class="form-control <?php echo isset($erreurs['pseudo']) ? 'is-invalid' : ''; ?>" 
                                id="pseudo" 
                                name="pseudo" 
                                value="<?php echo htmlspecialchars($pseudo); ?>" 
                                placeholder="Entrez un l'identifiant de l'utilisateur">
                            <?php if (isset($erreurs['pseudo'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['pseudo']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="motDePasse" class="form-label">Mot de passe</label>
                            <input type="password" 
                                class="form-control <?php echo isset($erreurs['motDePasse']) ? 'is-invalid' : ''; ?>" 
                                id="motDePasse" 
                                name="motDePasse" 
                                placeholder="Entrez un mot de passe sécurisé">
                            <?php if (isset($erreurs['motDePasse'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['motDePasse']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="confirmeMotDePasse" class="form-label">Confirmation du Mot de passe</label>
                            <input type="password" 
                                class="form-control <?php echo isset($erreurs['confirmeMotDePasse']) ? 'is-invalid' : ''; ?>" 
                                id="confirmeMotDePasse" 
                                name="confirmeMotDePasse" 
                                placeholder="Confirmez le mot de passe">
                            <?php if (isset($erreurs['confirmeMotDePasse'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['confirmeMotDePasse']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" 
                                class="form-control <?php echo isset($erreurs['prenom']) ? 'is-invalid' : ''; ?>" 
                                id="prenom" 
                                name="prenom" 
                                value="<?php echo htmlspecialchars($prenom); ?>" 
                                placeholder="Entrez le prénom">
                            <?php if (isset($erreurs['prenom'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['prenom']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" 
                                class="form-control <?php echo isset($erreurs['nom']) ? 'is-invalid' : ''; ?>" 
                                id="nom" 
                                name="nom" 
                                value="<?php echo htmlspecialchars($nom); ?>" 
                                placeholder="Entrez le nom">
                            <?php if (isset($erreurs['nom'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['nom']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Numéro de téléphone</label>
                            <input type="tel" 
                                class="form-control <?php echo isset($erreurs['telephone']) ? 'is-invalid' : ''; ?>" 
                                id="telephone" 
                                name="telephone" 
                                value="<?php echo htmlspecialchars($telephone); ?>" 
                                placeholder="Exemple : 1234">
                            <?php if (isset($erreurs['telephone'])): ?>
                                <div class="invalid-feedback"><?php echo $erreurs['telephone']; ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if (isset($erreurs['existance'])): ?>
                            <div class="alert alert-danger"><?php echo $erreurs['existance']; ?></div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Modale Modifier Utilisateur -->
    <div class="modal fade <?php echo !empty($erreursModif) ? 'show' : ''; ?>" 
        id="modalMofifierUtilisateur" 
        style="<?php echo !empty($erreursModif) ? 'display: block;' : 'display: none;'; ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalModifierUtilisateurLabel">Modifier un utilisateur</h5>
                    <a href="utilisateurs.php" class="btn-close" aria-label="Close"></a>
                </div>
                <div class="modal-body">
                <form id="formModifierUtilisateur" method="POST" action="utilisateurs.php">
                    <!-- Champ pour l'ID de l'utilisateur (caché) -->
                    <input type="hidden" id="idUtilisateur" name="idUtilisateur" value="">

                    <!-- Champ pour le pseudo -->
                    <div class="mb-3">
                        <label for="pseudoUtilisateur" class="form-label">Identifiant</label>
                        <input type="text" class="form-control <?php echo isset($erreursModif['pseudo']) ? 'is-invalid' : ''; ?>" id="pseudoUtilisateur" name="pseudoUtilisateur" placeholder="Modifiez l'identifiant" value="<?php echo htmlspecialchars($pseudo); ?>">
                        <?php if (isset($erreursModif['pseudo'])) { ?>
                            <div class="invalid-feedback"><?php echo $erreursModif['pseudo']; ?></div>
                        <?php } ?>
                    </div>

                    <!-- Champ pour le prénom -->
                    <div class="mb-3">
                        <label for="prenomUtilisateur" class="form-label">Prénom</label>
                        <input type="text" class="form-control <?php echo isset($erreursModif['prenom']) ? 'is-invalid' : ''; ?>" id="prenomUtilisateur" name="prenomUtilisateur" placeholder="Modifiez le prénom" value="<?php echo htmlspecialchars($prenom); ?>">
                        <?php if (isset($erreursModif['prenom'])) { ?>
                            <div class="invalid-feedback"><?php echo $erreursModif['prenom']; ?></div>
                        <?php } ?>
                    </div>

                    <!-- Champ pour le nom -->
                    <div class="mb-3">
                        <label for="nomUtilisateur" class="form-label">Nom</label>
                        <input type="text" class="form-control <?php echo isset($erreursModif['nom']) ? 'is-invalid' : ''; ?>" id="nomUtilisateur" name="nomUtilisateur" placeholder="Modifiez le nom" value="<?php echo htmlspecialchars($nom); ?>">

                        <?php if (isset($erreursModif['nom'])) { ?>
                            <div class="invalid-feedback"><?php echo $erreursModif['nom']; ?></div>
                        <?php } ?>
                    </div>

                    <!-- Champ pour le numéro de téléphone -->
                    <div class="mb-3">
                        <label for="telephoneUtilisateur" class="form-label">Numéro de téléphone</label>
                        <input type="tel" class="form-control <?php echo isset($erreursModif['telephone']) ? 'is-invalid' : ''; ?>" id="telephoneUtilisateur" name="telephoneUtilisateur" placeholder="Modifiez le numéro de téléphone"  value="<?php echo htmlspecialchars($telephone);?>">
                        <?php if (isset($erreursModif['telephone'])) { ?>
                            <div class="invalid-feedback"><?php echo $erreursModif['telephone']; ?></div>
                        <?php } ?>
                    </div>

                    <!-- Champ pour le nouveau mot de passe -->
                    <div class="mb-3">
                        <label for="motDePasseUtilisateur" class="form-label <?php echo isset($erreursModif['motDePasse']) ? 'is-invalid' : ''; ?>">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="motDePasseUtilisateur" name="motDePasseUtilisateur" placeholder="Entrez un nouveau mot de passe">
                        <?php if (isset($erreursModif['motDePasse'])) { ?>
                            <div class="invalid-feedback"><?php echo $erreursModif['motDePasse']; ?></div>
                        <?php } ?>
                    </div>

                    <!-- Champ pour confirmer le nouveau mot de passe -->
                    <div class="mb-3">
                        <label for="confirmeMotDePasseUtilisateur" class="form-label <?php echo isset($erreursModif['confirmeMotDePasse']) ? 'is-invalid' : ''; ?>">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="confirmeMotDePasseUtilisateur" name="confirmeMotDePasseUtilisateur" placeholder="Confirmez le mot de passe">
                        <?php if (isset($erreursModif['confirmeMotDePasse'])) { ?>
                            <div class="invalid-feedback"><?php echo $erreursModif['confirmeMotDePasse']; ?></div>
                        <?php } ?>
                    </div>

                    <?php if (isset($erreursModif['existance'])) { ?>
                            <div class="alert alert-danger"><?php echo $erreursModif['existance']; ?></div>
                    <?php } ?>
                    <!-- Bouton pour soumettre le formulaire -->
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </form>
                </div>
            </div>
        </div>
    </div>
  

    <!-- Modale de Confirmation -->
    <div class="modal <?php echo $utilisateurCree ? 'show' : ''; ?>" 
        id="modalConfirmation" 
        tabindex="-1" 
        aria-labelledby="modalConfirmationLabel" 
        aria-hidden="<?php echo $utilisateurCree ? 'false' : 'true'; ?>" 
        style="<?php echo $utilisateurCree ? 'display: block;' : 'display: none;'; ?>">
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
</div>
<?php require("../ressources/footer.php");?>
</body>
</html>