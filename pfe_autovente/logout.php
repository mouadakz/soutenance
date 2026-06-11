<?php
// Démarrer la session actuelle
session_start();

// Détruire toutes les données de la session
session_destroy();

// Rediriger l'utilisateur vers la page de connexion
header('Location: login.php');

// Arrêter l'exécution du script
exit();
?>