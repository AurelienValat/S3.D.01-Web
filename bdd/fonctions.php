<?php
// Fonction pour vérifier les identifiants et mdp
	function verifLoginMDP($pdo, $login, $mdp) {
		$mdpHash = hash('sha256',$mdp); 
		$stmt = $pdo->prepare("SELECT id_utilisateur, nom_utilisateur FROM utilisateurs WHERE nom_utilisateur = :login AND mot_de_passe = :mdp");
		$stmt->bindParam("login", $login);
		$stmt->bindParam("mdp", $mdpHash);
		$stmt->execute();
		return $stmt->fetch();
	}

    // Fonction pour démarrer une session et stocker les informations utilisateur
	function startSession($user) {
		session_start();
		$_SESSION['user_id'] = $user['id_utilisateur'];
		$_SESSION['user_login'] = $user['nom_utilisateur'];
	}

    // Fonction pour vérifier si un utilisateur est connecté
	function verifSession() {
		session_start();
		if (!isset($_SESSION['user_id'])) {
			header('Location: ../index.php');
			exit;
		}
	}


?>