<?php
// Démarrer la session pour accéder aux variables de session
session_start();

// Vérifier si un client est connecté
if (isset($_SESSION['client'])) {

    // Rediriger le client vers sa page d'accueil
    header('Location: client/home.php');

} elseif (isset($_SESSION['admin'])) {

    // Rediriger l'administrateur vers son tableau de bord
    header('Location: admin/dashboard.php');

} else {

    // Si aucun utilisateur n'est connecté,
    // rediriger vers la page d'accueil du client
    header('Location: client/home.php');
}

// Arrêter l'exécution du script après la redirection
exit();
?>