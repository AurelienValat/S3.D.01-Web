<?php
require_once ('fonctions.php');

// Récupère la liste des utilisteurs/employés
function getUtilisateurs($pdo) {
    return envoyerRequete("SELECT nom_utilisateur AS identifiant, nom, prenom, no_tel, est_admin FROM employe", $pdo);
}

// Récupèle la liste des exxpositions
function getExpositions($pdo) {
    return envoyerRequete("SELECT intitule, periode_oeuvres, nombre_oeuvres, mots_cles, resume, date_debut, date_fin FROM exposition", $pdo);
}

// Fonction qui affiche tous les conférenciers
function afficherConferenciers($pdo){
    try {
        $conferenciers = array();
        
        $requete = 'SELECT nom, prenom, specialite, no_tel, est_employe_par_musee, mots_cles_specialite FROM conferencier ORDER BY nom';
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
    return envoyerRequete("SELECT id_visite, exposition.intitule, conferencier.nom AS nom_conferencier, conferencier.prenom AS prenom_conferencier, employe.nom AS nom_employe, employe.prenom AS prenom_employe, horaire_debut, date_visite, intitule_client, no_tel_client
                           FROM visite

                           INNER JOIN exposition
                           ON exposition.id_exposition = visite.id_exposition
                            
                           INNER JOIN conferencier
                           ON conferencier.id_conferencier = visite.id_conferencier
                            
                           INNER JOIN employe
                           ON employe.id_employe = visite.id_employe;"
                          , $pdo);
}
