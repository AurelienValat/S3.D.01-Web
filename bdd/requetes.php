<?php
require_once ('fonctions.php');

function getUtilisateurs($pdo) {
    return envoyerRequete("SELECT nom_utilisateur AS identifiant, nom, prenom, no_tel, est_admin FROM employe", $pdo);
}