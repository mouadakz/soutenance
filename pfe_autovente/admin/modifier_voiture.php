<?php
// Démarrer la session et connexion à la base de données
session_start();
require '../config/db.php';

// Vérifier si l'administrateur est connecté
if (!isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit();
}

// Récupérer l'ID de la voiture depuis l'URL
// Si l'ID n'existe pas, rediriger vers la liste
$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: voitures.php');
    exit();
}

// Traitement du formulaire de modification
if (isset($_POST['modifier'])) {

    // Conserver l'image actuelle par défaut
    $img = $_POST['image_actuelle'];

    // Vérifier si une nouvelle image a été uploadée
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        // Vérifier l'extension de l'image
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {

            // Générer un nom unique pour l'image
            $img = 'voiture_' . time() . '.' . $ext;

            // Déplacer l'image vers le dossier cible
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/image/' . $img);
        }
    }

    // Mise à jour des données de la voiture dans la base de données
    $pdo->prepare("
        UPDATE voiture
        SET marque=?, modele=?, prix=?, statut=?, carburant=?, couleur=?, image=?
        WHERE idVoiture=?
    ")->execute([
        $_POST['marque'],
        $_POST['modele'],
        $_POST['prix'],
        $_POST['statut'],
        $_POST['carburant'],
        $_POST['couleur'],
        $img,
        $id
    ]);

    header('Location: voitures.php');
    exit();
}

// Récupérer les informations de la voiture à modifier
$stmt = $pdo->prepare("SELECT * FROM voiture WHERE idVoiture=?");
$stmt->execute([$id]);
$v = $stmt->fetch();

// Si la voiture n'existe pas, rediriger
if (!$v) {
    header('Location: voitures.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier Voiture - AutoVent</title>

    <!-- Feuille de style admin -->
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<!-- Barre de navigation -->
<?php include 'nav.php'; ?>

<div class="container">

    <h1>Modifier Voiture</h1>

    <div class="form-card">

        <form method="POST" enctype="multipart/form-data">

            <!-- Image actuelle stockée en hidden -->
            <input type="hidden" name="image_actuelle" value="<?= htmlspecialchars($v['image']) ?>">

            <div class="form-grid">

                <!-- Marque -->
                <div>
                    <label>Marque</label>
                    <input type="text" name="marque"
                           value="<?= htmlspecialchars($v['marque']) ?>" required>
                </div>

                <!-- Modèle -->
                <div>
                    <label>Modèle</label>
                    <input type="text" name="modele"
                           value="<?= htmlspecialchars($v['modele']) ?>" required>
                </div>

                <!-- Prix -->
                <div>
                    <label>Prix (DH)</label>
                    <input type="number" name="prix"
                           value="<?= $v['prix'] ?>" required>
                </div>

                <!-- Statut -->
                <div>
                    <label>Statut</label>
                    <select name="statut">
                        <option value="disponible" <?= $v['statut'] === 'disponible' ? 'selected' : '' ?>>
                            Disponible
                        </option>
                        <option value="reservee" <?= $v['statut'] === 'reservee' ? 'selected' : '' ?>>
                            Réservée
                        </option>
                        <option value="vendue" <?= $v['statut'] === 'vendue' ? 'selected' : '' ?>>
                            Vendue
                        </option>
                    </select>
                </div>

                <!-- Carburant -->
                <div>
                    <label>Carburant</label>
                    <select name="carburant">
                        <option value="essence" <?= $v['carburant'] === 'essence' ? 'selected' : '' ?>>
                            Essence
                        </option>
                        <option value="diesel" <?= $v['carburant'] === 'diesel' ? 'selected' : '' ?>>
                            Diesel
                        </option>
                        <option value="electrique" <?= $v['carburant'] === 'electrique' ? 'selected' : '' ?>>
                            Electrique
                        </option>
                        <option value="hybride" <?= $v['carburant'] === 'hybride' ? 'selected' : '' ?>>
                            Hybride
                        </option>
                    </select>
                </div>

                <!-- Couleur -->
                <div>
                    <label>Couleur</label>
                    <input type="text" name="couleur"
                           value="<?= htmlspecialchars($v['couleur']) ?>" required>
                </div>

            </div>

            <!-- Image actuelle + upload -->
            <div style="margin-bottom:18px;">

                <label>Image</label>

                <?php if ($v['image']): ?>
                    <img src="../assets/image/<?= htmlspecialchars($v['image']) ?>"
                         style="width:120px;height:80px;object-fit:cover;border-radius:8px;margin:8px 0;display:block;">
                <?php endif; ?>

                <input type="file" name="image" accept="image/*">

            </div>

            <!-- Boutons -->
            <div style="display:flex;gap:12px;">

                <button type="submit" name="modifier" class="btn-add">
                    Enregistrer
                </button>

                <a href="voitures.php" class="btn-edit"
                   style="padding:9px 20px;font-size:14px;">
                    Annuler
                </a>

            </div>

        </form>

    </div>

</div>

</body>
</html>