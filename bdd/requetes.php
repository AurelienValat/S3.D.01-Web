<?php
require_once ('fonctions.php');

function getUtilisateurs($pdo) {
    return envoyerRequete("SELECT nom_utilisateur AS identifiant, nom, prenom, no_tel, est_admin FROM employe", $pdo);
}

function getExpositions($pdo) {
    return envoyerRequete("SELECT intitule, periode_oeuvres, nombre_oeuvres, mots_cles, resume, date_debut, date_fin FROM exposition", $pdo);
}

// Fonction qui affiche tous les conférenciers
function afficherConferenciers($pdo){
    try {
        $conferenciers = array();
        
        $requete = 'SELECT DISTINCT nom, prenom, est_employe_par_musee, mots_cles_specialite FROM conferencier ORDER BY nom';
        $resultats = $pdo->query($requete);
        
        while ($ligne = $resultats->fetch()) {
            $conferenciers[] = $ligne; // Ajoute chaque conférencier au tableau
        }
        return $conferenciers; // Retourne le tableau des conférenciers
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
}