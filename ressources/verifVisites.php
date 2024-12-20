<?php
// TODO tester si ajout pas cassé
function verifVisites($pdo, $erreurs, $horaire_debut, $intitule_client, $no_tel_client, $id_conferencier, $date_visite, $id_exposition) {    
    // Validation de la date de visite
    if (($date_visite == "") || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $date_visite)) {
        $erreurs['date_visite'] = "Date invalide.";
    } else {
        // Vérification du jour (mardi à dimanche)
        $jour_semaine = date('N', strtotime($date_visite)); // 1 = lundi, 7 = dimanche
        if ($jour_semaine == 1) { // 1 correspond à lundi
            $erreurs['date_visite'] = "Les visites ne peuvent pas avoir lieu le lundi.";
        } else {
            // Vérifier si la date est entre aujourd'hui et dans 3 ans
            $aujourd_hui = new DateTime();
            $aujourd_hui->setTime(0, 0); // Normaliser l'heure à 00:00:00
            $date_max = (clone $aujourd_hui)->modify('+3 years');
            $date_visite_obj = DateTime::createFromFormat('Y-m-d', $date_visite);
            
            if (!$date_visite_obj) {
                $erreurs['date_visite'] = "Format de date incorrect.";
            } else {
                $date_visite_obj->setTime(0, 0); // Normaliser l'heure à 00:00:00
                if ($date_visite_obj < $aujourd_hui) {
                    $erreurs['date_visite'] = "La date doit être aujourd'hui ou dans le futur.";
                } elseif ($date_visite_obj > $date_max) {
                    $erreurs['date_visite'] = "La date ne peut pas dépasser 3 ans à partir d'aujourd'hui.";
                }
            }
        }
    }
    
    echo "l'heure :";
    var_dump($horaire_debut);
    
    
    if (($horaire_debut == "") || !preg_match("/^(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d$/", $horaire_debut)) {
        $erreurs['horaire_debut'] = "Heure invalide.";
    } else {
        // Vérification des horaires d'ouverture
        $heure = (int) substr($horaire_debut, 0, 2);
        if ($heure < 9 || $heure >= 18) {
            $erreurs['horaire_debut'] = "Les visites doivent avoir lieu entre 9 heures et 19 heures.";
        }
    }
    
    if (($intitule_client == "") || strlen($intitule_client) > 50) {
        $erreurs['intitule_client'] = "L’intitulé client est requis et ne doit pas dépasser 50 caractères.";
    }
    if (!preg_match("/^[0-9]{4}$/", $no_tel_client) && $no_tel_client != "") {
        $erreurs['no_tel_client'] = 'Numéro de téléphone invalide. Il doit contenir 4 chiffre.';
    }
    if (empty($erreurs)) {
        if (!verifierDisponibiliteConferencier($pdo, $id_conferencier, $date_visite, $horaire_debut)) {
            $erreurs['horaire_debut'] = "Le conférencier n’est pas disponible à cet horaire.";
        }
        if (!verifierEspacementVisites($pdo, $id_exposition, $date_visite, $horaire_debut)) {
            $erreurs['horaire_debut'] = "Les visites doivent être espacées de 10 minutes.";
        }
    }
    return $erreurs;
}