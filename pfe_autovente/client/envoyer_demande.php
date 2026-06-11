<?php
// Démarrage de la session et connexion à la base de données
session_start();
require '../config/db.php';

// Vérification que le client est connecté, sinon redirection vers login
if (!isset($_SESSION['client'])) {
    header('Location: ../login.php');
    exit();
}

// Récupération de l'ID de la voiture depuis l'URL (sécurisé avec cast int)
$idVoiture = (int)($_GET['id'] ?? 0);
$idClient  = $_SESSION['client'];

// Vérification que la voiture existe et qu'elle est disponible
$stmt = $pdo->prepare("SELECT * FROM voiture WHERE idVoiture = ? AND statut = 'disponible'");
$stmt->execute([$idVoiture]);
$voiture = $stmt->fetch();

// Si la voiture n'existe pas ou n'est plus disponible, retour à l'accueil
if (!$voiture) {
    header('Location: home.php');
    exit();
}

// Initialisation des messages de retour
$success = $erreur = '';

// Traitement du formulaire lors de la soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Nettoyage du commentaire pour éviter les failles XSS
    $commentaire = htmlspecialchars(trim($_POST['commentaire'] ?? ''), ENT_QUOTES, 'UTF-8');

    // Vérification que le client n'a pas déjà envoyé une demande pour ce véhicule
    $stmt = $pdo->prepare("SELECT idDemande FROM demande WHERE idClient = ? AND idVoiture = ?");
    $stmt->execute([$idClient, $idVoiture]);

    if ($stmt->fetch()) {
        // Demande déjà existante : affichage d'une erreur
        $erreur = "Vous avez déjà envoyé une demande pour ce véhicule.";
    } else {
        // Insertion de la nouvelle demande en base de données
        $pdo->prepare("INSERT INTO demande (commentaire, idClient, idVoiture) VALUES (?, ?, ?)")
            ->execute([$commentaire, $idClient, $idVoiture]);
        $success = "Votre demande a été envoyée avec succès!";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réservation - AutoVent</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<!-- Formulaire de demande de réservation -->
<div class="reservation-form">
    <h1>Demande de Réservation</h1>

    <!-- Informations résumées du véhicule sélectionné -->
    <div class="voiture-info">
        <h3><?= htmlspecialchars($voiture['marque'] . ' ' . $voiture['modele']) ?></h3>
        <p>Prix: <?= number_format($voiture['prix'], 0, ',', ' ') ?> DH</p>
        <p>Carburant: <?= htmlspecialchars($voiture['carburant']) ?> | Couleur: <?= htmlspecialchars($voiture['couleur']) ?></p>
    </div>

    <?php if ($success): ?>
        <!-- Message de succès après envoi de la demande -->
        <p class="success"><?= $success ?></p>
        <a href="home.php" style="color:#001E50;">← Retour aux véhicules</a>

    <?php else: ?>
        <!-- Affichage de l'erreur si elle existe -->
        <?php if ($erreur): ?>
            <p class="erreur"><?= $erreur ?></p>
        <?php endif; ?>

        <!-- Formulaire avec champ commentaire optionnel -->
        <form method="POST">
            <label style="color:#333; font-size:14px; display:block; margin-bottom:8px;">
                Commentaire (optionnel)
            </label>
            <textarea name="commentaire" placeholder="Ajoutez un message..."></textarea>
            <button type="submit" class="btn-submit">Envoyer la demande </button>
        </form>

    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
