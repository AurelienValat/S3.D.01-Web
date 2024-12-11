<?php
// Détruit la session
session_start();


// Redirige l'utilisateur vers la page index
header("Location: ../index.php");
exit;
?>