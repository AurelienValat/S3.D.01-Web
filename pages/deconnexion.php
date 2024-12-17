<?php
// Détruit la session
session_start();
session_destroy();
unset $_SESSION;

// Redirige l'utilisateur vers la page index
header("Location: ../index.php");
exit;
?>