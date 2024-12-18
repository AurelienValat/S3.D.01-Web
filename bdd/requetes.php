<?php
require_once ('fonctions.php');

// Récupère la liste des utilisteurs/employés
function getUtilisateurs($pdo) {
    return envoyerRequete("SELECT id_employe, nom_utilisateur AS identifiant, nom, prenom, no_tel, est_admin FROM Employe", $pdo);
}

// Récupèle la liste des exxpositions
function getExpositions($pdo) {
    return envoyerRequete("SELECT id_exposition, intitule, periode_oeuvres, nombre_oeuvres, mots_cles, resume, date_debut, date_fin FROM Exposition", $pdo);
}

// Fonction qui affiche tous les conférenciers
function afficherConferenciers($pdo){
    try {
        $conferenciers = array();
        
        $requete = 'SELECT id_conferencier, nom, prenom, specialite, no_tel, est_employe_par_musee, mots_cles_specialite FROM Conferencier ORDER BY nom';
        $resultats = $pdo->query($requete);
        
        while ($ligne = $resultats->fetch()) {
            $conferenciers[] = $ligne; // Ajoute chaque conférencier au tableau
        }
        return $conferenciers; // Retourne le tableau des conférenciers
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
}

// Récupère la liste des visites avec des noms à la place des ID pour les clés étrangères
function getVisites($pdo) {
    return envoyerRequete("SELECT id_visite, Exposition.intitule, Conferencier.nom AS nom_conferencier, Conferencier.prenom AS prenom_conferencier, Employe.nom AS nom_employe, Employe.prenom AS prenom_employe, horaire_debut, date_visite, intitule_client, no_tel_client
                           FROM Visite

                           INNER JOIN Exposition
                           ON Exposition.id_exposition = Visite.id_exposition
                            
                           INNER JOIN Conferencier
                           ON Conferencier.id_conferencier = Visite.id_conferencier
                            
                           INNER JOIN Employe
                           ON Employe.id_employe = Visite.id_employe;"
                          , $pdo);
}


// Supprime la ligne correspondant à l'ID en paramètre
function supprimerLigne($pdo, $id, $table) {
    try {
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id_". strtolower($table) ." =:id");
        
        $stmt->bindparam("id", $id);
        $stmt -> execute();
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
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

// Vérifie qu'il n'y a pas d'identifiant, et d'homonyme lors de la création d'un employé 
function verifierExistanceUtilisateur($pdo, $pseudo, $nom, $prenom) {
    $stmt = $pdo->prepare("SELECT COUNT(*) 
    FROM employe 
    WHERE nom_utilisateur = ? 
    OR (nom = ? AND prenom = ?)");
    $stmt->execute([$pseudo, $nom, $prenom]);
    return $stmt->fetchColumn() > 0;
}

// Crée un employé 
function creerEmploye($pdo, $pseudo, $nom, $prenom, $telephone, $motDePasse) {
    $motDePasseHash = hash('sha256', $motDePasse);

    $stmt = $pdo->prepare("INSERT INTO employe (nom_utilisateur, nom, prenom, no_tel, mot_de_passe, est_admin) 
    VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$pseudo, $nom, $prenom, $telephone, $motDePasseHash, 0]);


}

// Crée un Conférencier 
function creerConferencier($pdo, $nom, $prenom, $type, $specialite, $motSpecialite, $telephone) {
    $stmt = $pdo->prepare("INSERT INTO conferencier (nom, prenom, specialite, mots_cles_specialite, no_tel, est_employe_par_musee) 
    VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $specialite, $motSpecialite, $telephone, $type]);
}

// Vérifie d'homonyme lors de la création d'un conférencier 
function verifierExistanceConferencier($pdo, $nom, $prenom) {
    $stmt = $pdo->prepare("SELECT COUNT(*) 
    FROM conferencier 
    WHERE (nom = ? AND prenom = ?)");
    $stmt->execute([$nom, $prenom]);
    return $stmt->fetchColumn() > 0;
}

function recupExpositions($pdo){
    return envoyerRequete("SELECT id_exposition,intitule FROM exposition", $pdo);
}

function recupConferenciers($pdo){
    return envoyerRequete("SELECT id_conferencier, nom, prenom FROM conferencier", $pdo);
}





