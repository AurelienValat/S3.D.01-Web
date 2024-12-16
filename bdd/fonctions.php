<?php
// Fonction pour vérifier les identifiants et mdp
function verifLoginMDP($pdo, $login, $mdp) {
    $mdpHash = hash('sha256', $mdp);
    $stmt = $pdo->prepare("SELECT nom, prenom, id_employe, nom_utilisateur, est_admin FROM Employe WHERE nom_utilisateur = :login AND mot_de_passe = :mdp");
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

function supprimerEmploye($pdo, $id) {
    try {
        $stmt = $pdo->prepare('DELETE FROM Employe WHERE id_employe =:id');

        $stmt->bindparam("id", $id);
        $stmt -> execute();
        $employe = $stmt->fetch();
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
}
?>