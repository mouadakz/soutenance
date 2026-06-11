<?php
// Démarrage de la session
session_start();

// Connexion à la base de données
require '../config/db.php';

// Vérification de l'authentification de l'administrateur
if (!isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit();
}

// Annulation d'une réservation et remise du véhicule en état disponible
if (isset($_GET['annuler'])) {
    $pdo->prepare("UPDATE voiture SET statut='disponible' WHERE idVoiture=?")
        ->execute([(int)$_GET['annuler']]);

    header('Location: demande.php');
    exit();
}

// Suppression d'une demande
if (isset($_GET['supprimer'])) {
    $pdo->prepare("DELETE FROM demande WHERE idDemande=?")
        ->execute([(int)$_GET['supprimer']]);

    header('Location: demande.php');
    exit();
}

// Récupération de toutes les demandes avec les informations des clients et des véhicules
$demandes = $pdo->query("
    SELECT d.idDemande, d.dateDemande, d.commentaire,
           c.nom, c.prenom, c.email, c.telephone,
           v.idVoiture, v.marque, v.modele, v.prix, v.statut
    FROM demande d
    JOIN client c ON d.idClient = c.idClient
    JOIN voiture v ON d.idVoiture = v.idVoiture
    ORDER BY d.dateDemande DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Demandes - AutoVent Admin</title>

    <!-- Fichier CSS de l'administration -->
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<!-- Barre de navigation -->
<?php include 'nav.php'; ?>

<div class="container">

    <h1>Gestion des Demandes</h1>

    <?php if (!$demandes): ?>

        <!-- Message affiché lorsqu'aucune demande n'est enregistrée -->
        <div class="empty">
            <p>Aucune demande pour le moment.</p>
        </div>

    <?php else: ?>

        <!-- Tableau des demandes -->
        <table>
            <tr>
                <th>#</th>
                <th>Client</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Voiture</th>
                <th>Prix</th>
                <th>Statut</th>
                <th>Commentaire</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($demandes as $d): ?>
            <tr>

                <!-- Identifiant de la demande -->
                <td><?= $d['idDemande'] ?></td>

                <!-- Informations du client -->
                <td><?= htmlspecialchars($d['nom'].' '.$d['prenom']) ?></td>
                <td><?= htmlspecialchars($d['email']) ?></td>
                <td><?= htmlspecialchars($d['telephone']) ?></td>

                <!-- Informations du véhicule -->
                <td><?= htmlspecialchars($d['marque'].' '.$d['modele']) ?></td>

                <!-- Affichage du prix formaté -->
                <td><?= number_format($d['prix'], 0, ',', ' ') ?> DH</td>

                <!-- Affichage du statut du véhicule -->
                <td>
                    <span class="badge badge-<?= $d['statut'] ?>">
                        <?= ucfirst($d['statut']) ?>
                    </span>
                </td>

                <!-- Affichage du commentaire -->
                <td><?= $d['commentaire'] ? htmlspecialchars($d['commentaire']) : '-' ?></td>

                <!-- Date de la demande -->
                <td><?= $d['dateDemande'] ?></td>

                <!-- Boutons d'action -->
                <td>

                    <!-- Remettre le véhicule disponible -->
                    <?php if ($d['statut'] === 'reservee'): ?>
                        <a href="?annuler=<?= $d['idVoiture'] ?>"
                           class="btn-edit"
                           onclick="return confirm('Remettre disponible ?')">
                            Disponible
                        </a>
                    <?php endif; ?>

                    <!-- Supprimer la demande -->
                    <a href="?supprimer=<?= $d['idDemande'] ?>"
                       class="btn-del"
                       onclick="return confirm('Supprimer ?')">
                        Supprimer
                    </a>

                </td>

            </tr>
            <?php endforeach; ?>

        </table>

    <?php endif; ?>

</div>

</body>
</html>