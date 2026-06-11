<?php
// Démarrer la session et connexion à la base de données
session_start();
require '../config/db.php';

// Vérifier si l'administrateur est connecté
if (!isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit();
}

/* Fonction d’upload d’image */
function uploadImg($file) {

    if ($file['error'] !== 0) return '';

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) return '';

    $nom = 'voiture_' . time() . '.' . $ext;

    move_uploaded_file($file['tmp_name'], '../assets/image/' . $nom);

    return $nom;
}

/* Ajouter une voiture */
if (isset($_POST['ajouter'])) {

    $img = uploadImg($_FILES['image']);

    $pdo->prepare("
        INSERT INTO voiture (marque, modele, prix, statut, carburant, couleur, image)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ")->execute([
        $_POST['marque'],
        $_POST['modele'],
        $_POST['prix'],
        $_POST['statut'],
        $_POST['carburant'],
        $_POST['couleur'],
        $img
    ]);

    header('Location: voitures.php');
    exit();
}

/* Supprimer une voiture (FIX FOREIGN KEY ERROR) */
if (isset($_GET['supprimer'])) {

    $id = (int)$_GET['supprimer'];

    // supprimer les demandes liées avant la voiture
    $pdo->prepare("DELETE FROM demande WHERE idVoiture=?")
        ->execute([$id]);

    // supprimer la voiture
    $pdo->prepare("DELETE FROM voiture WHERE idVoiture=?")
        ->execute([$id]);

    header('Location: voitures.php');
    exit();
}

/* Récupérer les voitures */
$voitures = $pdo->query("
    SELECT * FROM voiture
    ORDER BY idVoiture DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Voitures - AutoVent Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<?php include 'nav.php'; ?>

<div class="container">

    <h1>Gestion des Voitures</h1>

    <div class="form-card">

        <h2>Ajouter une voiture</h2>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-grid">

                <input type="text" name="marque" placeholder="Marque" required>
                <input type="text" name="modele" placeholder="Modèle" required>
                <input type="number" name="prix" placeholder="Prix (DH)" required>

                <select name="statut">
                    <option value="disponible">Disponible</option>
                    <option value="reservee">Réservée</option>
                </select>

                <select name="carburant">
                    <option value="essence">Essence</option>
                    <option value="diesel">Diesel</option>
                    <option value="electrique">Electrique</option>
                    <option value="hybride">Hybride</option>
                </select>

                <input type="text" name="couleur" placeholder="Couleur" required>

            </div>

            <input type="file" name="image" accept="image/*" style="margin-bottom:15px;">

            <button type="submit" name="ajouter" class="btn btn-add">Ajouter</button>

        </form>

    </div>

    <h2>Liste des voitures</h2>

    <table>

        <tr>
            <th>Image</th>
            <th>Marque</th>
            <th>Modèle</th>
            <th>Prix</th>
            <th>Statut</th>
            <th>Carburant</th>
            <th>Couleur</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($voitures as $v): ?>
        <tr>

            <td>
                <img src="../assets/image/<?= $v['image'] ?: 'vw-logo.png' ?>" class="car-img">
            </td>

            <td><?= htmlspecialchars($v['marque']) ?></td>
            <td><?= htmlspecialchars($v['modele']) ?></td>
            <td><?= number_format($v['prix'], 0, ',', ' ') ?> DH</td>

            <td>
                <span class="badge badge-<?= $v['statut'] ?>">
                    <?= $v['statut'] ?>
                </span>
            </td>

            <td><?= htmlspecialchars($v['carburant']) ?></td>
            <td><?= htmlspecialchars($v['couleur']) ?></td>

            <td>
                <a href="modifier_voiture.php?id=<?= $v['idVoiture'] ?>" class="btn-edit">Modifier</a>
                <a href="?supprimer=<?= $v['idVoiture'] ?>" class="btn-del" onclick="return confirm('Supprimer ?')">Supprimer</a>
            </td>

        </tr>
        <?php endforeach; ?>

    </table>

</div>

</body>
</html>