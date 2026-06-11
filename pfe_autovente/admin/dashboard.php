<?php
// Démarrer la session
session_start();

// Inclure le fichier de connexion à la base de données
require '../config/db.php';

// Vérifier si l'administrateur est connecté
// Si ce n'est pas le cas, rediriger vers la page de connexion
if (!isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit();
}

// Récupérer le nombre total de véhicules
$total_voitures = $pdo->query("SELECT COUNT(*) FROM voiture")->fetchColumn();

// Récupérer le nombre total de demandes
$total_demandes = $pdo->query("SELECT COUNT(*) FROM demande")->fetchColumn();

// Récupérer le nombre de véhicules réservés
$voitures_reservees = $pdo->query("
    SELECT COUNT(*) 
    FROM voiture 
    WHERE statut = 'reservee'
")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - AutoVent</title>

    <!-- Feuille de style de l'administration -->
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

    <!-- Barre de navigation -->
    <?php include 'nav.php'; ?>

    <div class="dashboard">

        <!-- Titre principal -->
        <h1>Tableau de bord</h1>

        <!-- Cartes statistiques -->
        <div class="stats">

            <!-- Nombre total de véhicules -->
            <div class="stat-card">
                <h3>Total Véhicules</h3>
                <p><?= $total_voitures ?></p>
            </div>

            <!-- Nombre total de demandes -->
            <div class="stat-card">
                <h3>Total Demandes</h3>
                <p><?= $total_demandes ?></p>
            </div>

            <!-- Nombre de véhicules réservés -->
            <div class="stat-card">
                <h3>Véhicules Réservés</h3>
                <p><?= $voitures_reservees ?></p>
            </div>

        </div>

    </div>

</body>

</html>