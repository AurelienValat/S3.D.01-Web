<?php
    // Démarre la connexion à la BD et renvoie l'objet PDO 
    //si tout s'est bien passé, false sinon.
    function initierConnexion() {
        // Pour Uniform Server
        $host = 'localhost';  // Serveur de BD
        $db = 'Musee';  // Nom de la BD
        $user = 'root';       // User
        $pass = 'root';       // Mot de passe
        $charset = 'utf8mb4'; // charset utilisé
        
        // Pour le vrai serveur
//         $host = 'sql100.infinityfree.com';  // Serveur de BD
//         $db = 'if0_37855898_museoflow';  // Nom de la BD
//         $user = 'if0_37855898';       // User
//         $pass = 'IDKubjwbEJS4DG';       // Mot de passe
        
        // Constitution variable DSN
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        
        // Réglage des options
        $options = [
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES=>false
        ];
        
        // Bloc try bd injoignable ou si erreur SQL
        try {
            // Connexion PDO
            $pdo = new PDO($dsn, $user, $pass, $options);
            
        } catch (PDOException $e) {
            // Il y a eu une erreur
            return false;
        }
        return $pdo;
    }
    
    // Envoie la requette à la BD via l'objet PDO passé en 2ème param
    function envoyerRequette($requete, $pdo) {
        return $pdo->query($requete);
    }