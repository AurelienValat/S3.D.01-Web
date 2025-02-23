<?php
require_once ('fonctions.php');

// Récupère la liste des utilisteurs/employés
function getUtilisateurs($pdo) {
    return envoyerRequete("SELECT id_employe, 
                                  nom_utilisateur AS identifiant, 
                                  nom, 
                                  prenom, 
                                  no_tel, 
                                  est_admin 
                          FROM Employe 
                          ORDER BY nom, prenom", $pdo);
}

// Récupèle la liste des expositions
function getExpositions($pdo) {
    return envoyerRequete("SELECT id_exposition, 
                                  intitule, 
                                  periode_debut_oeuvres, 
                                  periode_fin_oeuvres, 
                                  nombre_oeuvres, 
                                  mots_cles, 
                                  resume, 
                                  DATE_FORMAT(date_debut, '%d/%m/%Y') AS date_debut, 
                                  DATE_FORMAT(date_fin, '%d/%m/%Y') AS date_fin 
                           FROM Exposition 
                           ORDER BY intitule", $pdo);
}
// Récupèle la liste de tout les conférenciers
function getConferenciers($pdo) {
    return envoyerRequete("SELECT id_conferencier, 
                                  nom, 
                                  prenom, 
                                  specialite, 
                                  mots_cles_specialite, 
                                  no_tel, 
                                  est_employe_par_musee 
                           FROM Conferencier 
                           ORDER BY nom, prenom", $pdo);
}

function rechercheConferenciers($pdo, $nomRecherche, $prenomRecherche, $typeRecherche, $specialiteRecherche, $motsClesRecherche) {
    try {
        $nomRecherche = '%' . $nomRecherche . '%';
        $prenomRecherche = '%' . $prenomRecherche . '%';
        $typeRecherche = '%' . $typeRecherche . '%';
        $specialiteRecherche = '%' . $specialiteRecherche . '%';
        $motsClesRecherche = '%' . $motsClesRecherche . '%';

        $stmt = $pdo->prepare("SELECT id_conferencier,
                                      nom,
                                      prenom,
                                      specialite,
                                      mots_cles_specialite,
                                      no_tel,
                                      est_employe_par_musee
                               FROM Conferencier
                               WHERE nom LIKE :conferencierRecherche1
                               AND prenom LIKE :conferencierRecherche2
                               AND est_employe_par_musee LIKE :conferencierRecherche3
                               AND specialite LIKE :specialiteRecherche
                               AND mots_cles_specialite LIKE :motsClesRecherche
                               ORDER BY nom, prenom");

        $stmt->bindValue(":conferencierRecherche1", $nomRecherche, PDO::PARAM_STR);
        $stmt->bindValue(":conferencierRecherche2", $prenomRecherche, PDO::PARAM_STR);
        $stmt->bindValue(":conferencierRecherche3", $typeRecherche, PDO::PARAM_STR);
        $stmt->bindValue(":specialiteRecherche", $specialiteRecherche, PDO::PARAM_STR);
        $stmt->bindValue(":motsClesRecherche", $motsClesRecherche, PDO::PARAM_STR);

        $stmt->execute();
        return $stmt;
    } catch (Exception $e) {
        throw $e;
    }
}

function rechercheUtilisateurs($pdo, $nomRecherche, $prenomRecherche) {
    try {
        $nomRecherche = '%' . $nomRecherche . '%';
        $prenomRecherche = '%' . $prenomRecherche . '%';
        $stmt = $pdo->prepare("SELECT id_employe, 
                                      nom_utilisateur AS identifiant, 
                                      nom, 
                                      prenom, 
                                      no_tel, 
                                      est_admin
                               FROM Employe
                               WHERE nom LIKE :nomRecherche
                               AND prenom LIKE :prenomRecherche
                               ORDER BY nom, prenom");
        $stmt->bindValue(":nomRecherche", $nomRecherche, PDO::PARAM_STR);
        $stmt->bindValue(":prenomRecherche", $prenomRecherche, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt;
    } catch (Exception $e) {
        throw $e;
    }
}

function rechercheVisite($pdo, $idExposition = null, $idConferencier = null, $dateDebut = '', $dateFin = '', $heureDebut = '') {
    try {
        $idExposition = $idExposition ? (int) $idExposition : null;
        $idConferencier = $idConferencier ? (int) $idConferencier : null;
        $dateDebut = $dateDebut ? $dateDebut : '0000-01-01';
        $dateFin = $dateFin ? $dateFin : '9999-12-31';
        $heureDebut = $heureDebut ? $heureDebut : '%';

        $stmt = $pdo->prepare("SELECT v.id_visite,
                                e.intitule AS intitule,
                                CONCAT(c.nom, ' ', c.prenom) AS nom_conferencier,
                                c.prenom AS prenom_conferencier,
                                emp.nom AS nom_employe,
                                emp.prenom AS prenom_employe,
                                v.intitule_client,
                                v.no_tel_client,
                                v.date_visite,
                                v.horaire_debut
                            FROM Visite v
                            INNER JOIN Exposition e ON v.id_exposition = e.id_exposition
                            INNER JOIN Conferencier c ON v.id_conferencier = c.id_conferencier
                            INNER JOIN Employe emp ON v.id_employe = emp.id_employe
                            WHERE (v.id_exposition = IFNULL(:idExposition, v.id_exposition))
                            AND (v.id_conferencier = IFNULL(:idConferencier, v.id_conferencier))
                            AND (v.date_visite BETWEEN :dateDebut AND :dateFin)
                            AND (v.horaire_debut LIKE :heureDebut)
                            ORDER BY v.date_visite, v.horaire_debut");

        $stmt->bindValue(":heureDebut", '%' . $heureDebut . '%', PDO::PARAM_STR);  
        $stmt->bindValue(":idExposition", $idExposition, PDO::PARAM_INT);
        $stmt->bindValue(":idConferencier", $idConferencier, PDO::PARAM_INT);
        $stmt->bindValue(":dateDebut", $dateDebut, PDO::PARAM_STR);
        $stmt->bindValue(":dateFin", $dateFin, PDO::PARAM_STR);

        $stmt->execute();
        return $stmt;
    } catch (Exception $e) {
        throw $e;
    }
}





// Récupèle la liste des conférenciers
function getIndisponibilites($pdo) {
    return envoyerRequete("SELECT id_indisponibilite, id_conferencier, debut, fin FROM Indisponibilite", $pdo);
}


// Récupère la liste des visites avec des noms à la place des ID pour les clés étrangères
function getVisites($pdo) {
    return envoyerRequete("SELECT id_visite, 
                                  Exposition.intitule, 
                                  Conferencier.nom AS nom_conferencier, 
                                  Conferencier.prenom AS prenom_conferencier, 
                                  Employe.nom AS nom_employe, 
                                  Employe.prenom AS prenom_employe, 
                                  DATE_FORMAT(horaire_debut, '%Hh%i') AS horaire_debut, 
                                  DATE_FORMAT(date_visite, '%d/%m/%Y') AS date_visite, 
                                  intitule_client, 
                                  no_tel_client
                           FROM Visite

                           INNER JOIN Exposition
                           ON Exposition.id_exposition = Visite.id_exposition
                            
                           INNER JOIN Conferencier
                           ON Conferencier.id_conferencier = Visite.id_conferencier
                            
                           INNER JOIN Employe
                           ON Employe.id_employe = Visite.id_employe
                           
                           ORDER BY date_visite, Exposition.intitule;"
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
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) 
        FROM Employe 
        WHERE nom_utilisateur = ? 
        OR (nom = ? AND prenom = ?)");

    $stmt->execute([$pseudo, $nom, $prenom]);
    return $stmt->fetchColumn() > 0;
}


// Vérifie qu'il n'y a pas d'identifiant, et d'homonyme lors de la modification d'un employé 
function verifierExistanceUtilisateurModif($pdo, $pseudo, $nom, $prenom, $id_employe) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) 
        FROM Employe 
        WHERE (nom_utilisateur = ? 
        OR (nom = ? AND prenom = ?))
        AND id_employe != ?");  

        $stmt->execute([$pseudo, $nom, $prenom, $id_employe]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la vérification des doublons.");
    }
}

// Crée un employé 
function creerEmploye($pdo, $pseudo, $nom, $prenom, $telephone, $motDePasse) {
    $motDePasseHash = hash('sha256', $motDePasse);

    $stmt = $pdo->prepare("INSERT INTO Employe (nom_utilisateur, nom, prenom, no_tel, mot_de_passe, est_admin) 
    VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$pseudo, $nom, $prenom, $telephone, $motDePasseHash, 0]);


}

// Crée un Conférencier 
function creerConferencier($pdo, $nom, $prenom, $type, $specialite, $motSpecialite, $telephone) {
    $stmt = $pdo->prepare("INSERT INTO Conferencier (nom, prenom, specialite, mots_cles_specialite, no_tel, est_employe_par_musee) 
    VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $specialite, $motSpecialite, $telephone, $type]);
}

// Crée une Visite
function creerVisite($pdo, $id_exposition, $id_conferencier, $id_employe, $horaire_debut, $date_visite, $intitule_client, $no_tel_client) {
    $stmt = $pdo->prepare("
        INSERT INTO Visite (id_exposition, id_conferencier, id_employe, horaire_debut, date_visite, intitule_client, no_tel_client) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$id_exposition, $id_conferencier, $id_employe, $horaire_debut, $date_visite, $intitule_client, $no_tel_client]);
}

// Crée Exposition
function creerExposition($pdo, $intitule, $annee_debut_oeuvres, $annee_fin_oeuvres, $nombre_oeuvres, $mots_cles, $resume, $date_debut, $date_fin) {
    if (empty($date_fin)) {
        $date_fin = NULL;
    }
    $stmt = $pdo->prepare("
    INSERT INTO Exposition (intitule, periode_debut_oeuvres, periode_fin_oeuvres, nombre_oeuvres, mots_cles, resume, date_debut, date_fin) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
    $stmt->execute([$intitule, $annee_debut_oeuvres, $annee_fin_oeuvres, $nombre_oeuvres, $mots_cles, $resume, $date_debut, $date_fin]);
}

// Fonction pour vérifier si l'exposition existe déjà
function expositionExiste($pdo, $intitule) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Exposition WHERE intitule = :intitule");
    $stmt->execute(['intitule' => $intitule]);
    return $stmt->fetchColumn() > 0;
}

// Vérifie d'homonyme lors de la création d'un conférencier 
function verifierExistanceConferencier($pdo, $nom, $prenom) {
    $stmt = $pdo->prepare("SELECT COUNT(*) 
    FROM Conferencier 
    WHERE nom = ? AND prenom = ?");
  
    $stmt->execute([$nom, $prenom]);
    return $stmt->fetchColumn() > 0;
}

// Vérifie d'homonyme lors de la modification d'un conférencier 
function verifierExistanceConferencierModif($pdo, $nom, $prenom, $idConferencier) {
    $stmt = $pdo->prepare("SELECT COUNT(*) 
    FROM Conferencier 
    WHERE (nom = ? AND prenom = ?)
    AND id_conferencier != ?");
    $stmt->execute([$nom, $prenom, $idConferencier]);
    return $stmt->fetchColumn() > 0;
}

function recupExpositions($pdo){
    return envoyerRequete("SELECT id_exposition,intitule FROM Exposition", $pdo);
}

function recupConferenciers($pdo){
    return envoyerRequete("SELECT id_conferencier, nom, prenom FROM Conferencier", $pdo);
}

/**
 * Vérifie si le conférencier est disponible.
 */
function verifierDisponibiliteConferencier($pdo, $id_conferencier, $date_visite, $horaire_debut) {
    $horaire_fin = date("H:i:s", strtotime("$horaire_debut +1 hour +30 minutes"));
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM Visite 
        WHERE id_conferencier = :id_conferencier
        AND date_visite = :date_visite
        AND NOT (horaire_debut >= :horaire_fin OR ADDTIME(horaire_debut, '01:30:00') <= :horaire_debut)
    ");
    $stmt->execute([
        'id_conferencier' => $id_conferencier,
        'date_visite' => $date_visite,
        'horaire_debut' => $horaire_debut,
        'horaire_fin' => $horaire_fin
    ]);
    return $stmt->fetchColumn() == 0;
}

/**
 * Vérifie l'espacement entre deux visites pour une même exposition.
 */
function verifierEspacementVisites($pdo, $id_exposition, $date_visite, $horaire_debut) {
    $horaire_precedent = date("H:i:s", strtotime("$horaire_debut -10 minutes"));
    $horaire_suivant = date("H:i:s", strtotime("$horaire_debut +1 hour +10 minutes"));
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM Visite 
        WHERE id_exposition = :id_exposition
        AND date_visite = :date_visite
        AND (horaire_debut BETWEEN :horaire_precedent AND :horaire_suivant)
    ");
    $stmt->execute([
        'id_exposition' => $id_exposition,
        'date_visite' => $date_visite,
        'horaire_precedent' => $horaire_precedent,
        'horaire_suivant' => $horaire_suivant
    ]);
    return $stmt->fetchColumn() == 0;
}
// TODO on suppose que 2 expositions ne peuvent pas avoir le même nom, VERIFIER AU PRES DU CLIENT
function modifierVisite($pdo, $exposition_concernee_modifie, $conferencier_modifie, $id_employe_modifie, $intitule_client_modifie, $no_tel_client_modifie, $date_visite_modifie, $horaire_debut_modifie, $id_visite_modifie) {
    $stmt = $pdo->prepare("
        UPDATE Visite 
        SET id_exposition = (SELECT Exposition.id_exposition
                             FROM Exposition
                             WHERE Exposition.intitule = :exposition_concernee_modifie), 

            id_conferencier = (SELECT Conferencier.id_conferencier
                               FROM Conferencier
                               WHERE CONCAT(Conferencier.nom, ' ', Conferencier.prenom) = :conferencier_modifie), 

            id_employe = (SELECT Employe.id_employe
                          FROM Employe
                          WHERE CONCAT(Employe.prenom, ' ', Employe.nom) = :id_employe_modifie), 

            horaire_debut = :horaire_debut_modifie, 
            date_visite = :date_visite_modifie, 
            intitule_client = :intitule_client_modifie, 
            no_tel_client = :no_tel_client_modifie 
        WHERE Visite.id_visite = :id_visite_modifie;
    ");
    $stmt->execute([
        'exposition_concernee_modifie' => $exposition_concernee_modifie,
        'conferencier_modifie' => $conferencier_modifie,
        'horaire_debut_modifie' => $horaire_debut_modifie,
        'date_visite_modifie' => $date_visite_modifie,
        'intitule_client_modifie' => $intitule_client_modifie,
        'no_tel_client_modifie' => $no_tel_client_modifie,
        'id_visite_modifie' => $id_visite_modifie,
        'id_employe_modifie' => $id_employe_modifie
    ]);
}

function modifUtilisateur($pdo, $idUtilisateur, $donnees) {
    try {
        $setClause = [];
        $params = [];

        // Construire les clauses et les valeurs dynamiquement
        foreach ($donnees as $colonne => $value) {
            $setClause[] = "`$colonne` = :$colonne";
            $params[":$colonne"] = $value;
        }

        // Ajouter la condition WHERE avec un paramètre nommé
        $params[':id'] = $idUtilisateur;

        // Construire la requête SQL
        $sql = "UPDATE Employe SET " . implode(', ', $setClause) . " WHERE id_employe = :id";
        $stmt = $pdo->prepare($sql);

        // Exécuter la requête
        return $stmt->execute($params);
    } catch (Exception $e) {
        throw $e; 
    }
}

function modifConferencier($pdo, $idConferencier, $donnees) {
    try {
        $setClause = [];
        $params = [];

        // Construire les clauses et les valeurs dynamiquement
        foreach ($donnees as $colonne => $value) {
            $setClause[] = "`$colonne` = :$colonne";
            $params[":$colonne"] = $value;
        }

        // Ajouter la condition WHERE avec un paramètre nommé
        $params[':id'] = $idConferencier;

        // Construire la requête SQL
        $sql = "UPDATE Conferencier SET " . implode(', ', $setClause) . " WHERE id_conferencier = :id";
        $stmt = $pdo->prepare($sql);

        // Exécuter la requête
        return $stmt->execute($params);
    } catch (Exception $e) {
        throw $e; 
    }
}


function modifExposition($pdo, $idExposition, $description) {
    try {
        $stmt = $pdo -> prepare("UPDATE Exposition SET resume = :description WHERE id_exposition = :id");
        $stmt ->bindParam("description", $description);
        $stmt ->bindParam("id", $idExposition);
        $stmt->execute();
    }catch (Exception $e) {
        throw $e; 
    }
}

function recupIndisponibilite($pdo, $idConferencier) {
    try {
        $sql = "SELECT id_indisponibilite, debut, fin FROM Indisponibilite WHERE id_conferencier = :idConferencier";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idConferencier', $idConferencier);  
        $stmt->execute();
        return $stmt ->fetchAll();
    } catch (Exception $e) {
        throw $e;
    }
}

// Exemple de vérification si une expo a une visite
function verifierVisitePourExpo($pdo, $idExposition) {
    $sql = "SELECT COUNT(*) FROM Visite WHERE id_exposition = :idExposition ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam('idExposition', $idExposition);
    $stmt->execute();
    
    $count = $stmt->fetchColumn();
    return $count > 0;  // Retourne true si il y a au moins une visite sur l'expo
}

function recupVisites($pdo, $idConferencier){
    try {
        $sql = "SELECT id_visite, date_visite 
                FROM Visite 
                JOIN Conferencier 
                ON Visite.id_conferencier = Conferencier.id_conferencier
                WHERE Conferencier.id_conferencier = :idConferencier";
            
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam('idConferencier', $idConferencier);
        $stmt->execute();
        return $stmt ->fetchAll();
    } catch (Exception $e) {
        throw $e;
    }

}

function recupNomConferencier($pdo, $idConferencier){
    try {
        $sql = "SELECT nom, prenom
            FROM Conferencier 
            WHERE id_conferencier = :idConferencier";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam('idConferencier', $idConferencier);
        $stmt->execute();
        return $stmt ->fetch();
    } catch (Exception $e) {
        throw $e;
    }
}

function creerIndisponibilite($pdo, $idConferencier, $debut, $fin) {
    try {
        $sql = "INSERT INTO Indisponibilite(id_conferencier, debut, fin) VALUES(:idConferencier, :debut, :fin)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam('idConferencier', $idConferencier);
        $stmt->bindParam('debut', $debut);
        $stmt->bindParam('fin', $fin);
        $stmt->execute();
    } catch (Exception $e) {
        throw $e;
    }
}

function modifierIndisponibilite($pdo, $idIndisponibilite, $idConferencier, $debut, $fin) {
    try {
        $sql = "UPDATE Indisponibilite SET debut = :debut, fin = :fin WHERE id_conferencier = :idConferencier AND id_indisponibilite = :idIndisponibilite";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam('idConferencier', $idConferencier);
        $stmt->bindParam('idIndisponibilite', $idIndisponibilite);
        $stmt->bindParam('debut', $debut);
        $stmt->bindParam('fin', $fin);
        $stmt->execute();
    } catch (Exception $e) {
        throw $e;
    }
}


function exportationTable($pdo, $table) {
    // Déterminer les colonnes à sélectionner en fonction de la table
    if ($table === 'employe') {
        $stmt = $pdo->query("SELECT id_employe,
                                        nom,
                                        prenom,
                                        no_tel
                                 FROM Employe
                                 ORDER BY nom, prenom");
        $rows = $stmt->fetchAll();
        
    } else if ($table === 'visite') {
        $stmt = $pdo->query("SELECT id_visite,
                                    id_exposition,
                                    id_conferencier,
                                    id_employe,
                                    DATE_FORMAT(date_visite, '%d/%m/%Y') AS date_visite, 
                                   DATE_FORMAT(horaire_debut, '%Hh%i') AS horaire_debut, 
                                    intitule_client,
                                    no_tel_client
                              FROM Visite
                              ORDER BY date_visite");
        $rows = $stmt->fetchAll();
        
    } else if ($table === 'exposition') {
        $stmt = $pdo->query("SELECT id_exposition, 
                                    intitule, 
                                    periode_debut_oeuvres, 
                                    periode_fin_oeuvres, 
                                    nombre_oeuvres, 
                                    mots_cles, 
                                    resume, 
                                    DATE_FORMAT(date_debut, '%d/%m/%Y') AS date_debut, 
                                    DATE_FORMAT(date_fin, '%d/%m/%Y') AS date_fin 
                             FROM Exposition 
                             ORDER BY intitule");
        $rows = $stmt->fetchAll();
        
    } else if ($table === 'conferencier') {
        $stmt = $pdo->query("SELECT id_conferencier,
                                    nom,
                                    prenom,
                                    mots_cles_specialite,
                                    no_tel,
                                    CASE
                                        WHEN est_employe_par_musee = 1 THEN 'oui'
                                        ELSE 'non'
                                    END AS est_employe_par_musee,

                                        -- Recuperation des indisponibilites
                                        (SELECT GROUP_CONCAT(CONCAT(DATE_FORMAT(debut,'%d/%m/%Y'), ';', DATE_FORMAT(fin,'%d/%m/%Y')) SEPARATOR ';') AS periodes_indisponibilites
                                        FROM Indisponibilite
                                        WHERE Indisponibilite.id_conferencier = Conferencier.id_conferencier
                                        ORDER BY debut) AS indisponibilite

                                 FROM Conferencier
                                 ORDER BY nom, prenom;");
        $rows = $stmt->fetchAll();        
    } else {
        // Le nom de la table est invalide
        return false;
    }
    // On retourne les lignes de la table
    return $rows;
}
