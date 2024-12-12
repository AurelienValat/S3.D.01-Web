<?php
require_once ('fonctions.php');

function getUtilisateurs($pdo) {
    return envoyerRequete("SELECT nom_utilisateur AS identifiant, nom, prenom, no_tel, est_admin FROM employe", $pdo);
}
// TODO 
function getExpositions($pdo) {
    return envoyerRequete("SELECT intitule, periode_oeuvres, nombre_oeuvres, mots_cles, resume, date_debut, date_fin FROM exposition", $pdo);
}