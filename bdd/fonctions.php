<?php
// Fonction pour vérifier les identifiants et mdp
function verifLoginMDP($pdo, $login, $mdp) {
    $mdpHash = hash('sha256', $mdp);
    $stmt = $pdo->prepare("SELECT nom, prenom, id_employe, nom_utilisateur, est_admin FROM employe WHERE nom_utilisateur = :login AND mot_de_passe = :mdp");
    $stmt->bindParam("login", $login);
    $stmt->bindParam("mdp", $mdpHash);
    $stmt->execute();
    return $stmt->fetch();
}

// Fonction pour vérifier si un utilisateur est connecté
function verifSession() {
    // Si la session n'existe plus, on redirige vers la page de connexion
    if (!isset($_SESSION['id'])) {
        header('Location: ../index.php');
        exit();
    }
}

// Pour réafficher la saisie utilisateur pour les champs texte
function reafficherSaisie($nomChamp) {
    // Protection contre l'injection de code
    $nomChamp = htmlspecialchars_decode($nomChamp);
    if (isset($_POST[$nomChamp]) && trim($_POST[$nomChamp]) !== '') {
        return $_POST[$nomChamp];
    } else {
        return '';
    }
}

// Pour réafficher la saisie utilisateur pour listes déroulantes
function reafficherSaisieOption($valeurOption, $nomChamp) {
    // Protection contre l'injection de code
    $nomChamp = htmlspecialchars_decode($nomChamp);
    $valeurOption = htmlspecialchars($valeurOption);
    if (isset($_POST[$nomChamp]) && $_POST[$nomChamp] === $valeurOption) {
        return 'selected';
    } else {
        return '';
    }
}

// Fonction qui affiche tous les conférenciers
function afficherConferenciers($pdo){
    try {
        $conferenciers = array();
        
        $requete = 'SELECT DISTINCT nom, prenom, est_employe_par_musee, mots_cles_specialite FROM conferencier ORDER BY nom';
        $resultats = $pdo->query($requete);
        
        while ($ligne = $resultats->fetch()) {
            $conferenciers[] = $ligne; // Ajoute chaque médicament au tableau
        }
        return $conferenciers; // Retourne le tableau des médicaments
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
}
?>