<?php
// Démarrage de la session et connexion à la base de données
session_start();
require '../config/db.php';

// Récupération des filtres de recherche depuis l'URL (GET)
$modele    = isset($_GET['modele'])    ? $_GET['modele']    : '';
$prix      = isset($_GET['prix'])      ? $_GET['prix']      : '';
$carburant = isset($_GET['carburant']) ? $_GET['carburant'] : '';

// Construction dynamique de la requête SQL selon les filtres
$query  = "SELECT * FROM voiture WHERE statut='disponible'";
$params = [];

// Ajout du filtre modèle si renseigné (recherche partielle avec LIKE)
if ($modele != '') {
    $query   .= " AND modele LIKE ?";
    $params[] = "%$modele%";
}

// Ajout du filtre prix maximum si renseigné
if ($prix != '') {
    $query   .= " AND prix <= ?";
    $params[] = $prix;
}

// Ajout du filtre type de carburant si renseigné
if ($carburant != '') {
    $query   .= " AND carburant = ?";
    $params[] = $carburant;
}

// Exécution de la requête avec les paramètres filtrés
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$voitures = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoVent - Home</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<!-- Section hero avec image principale et slogan -->
<section class="hero">
    <img src="../assets/image/volksw.png" alt="VW">
    <div class="hero-text">
        <h1>Drive the Future of Volkswagen.</h1>
        <p>Discover the perfect blend of electric innovation and cutting-edge technology at AutoVent.</p>
        <a href="#voitures">Voir les véhicules</a>
    </div>
</section>

<!-- Barre de recherche avec filtres -->
<section class="search">
    <form method="GET">
        <!-- Filtre par modèle -->
        <input type="text" name="modele" placeholder="Modèle" value="<?php echo $modele; ?>">

        <!-- Filtre par prix maximum -->
        <input type="number" name="prix" placeholder="Prix max" value="<?php echo $prix; ?>">

        <!-- Filtre par type de carburant -->
        <select name="carburant">
            <option value="">Tous</option>
            <option value="electrique" <?php echo $carburant == 'electrique' ? 'selected' : ''; ?>>Electrique</option>
            <option value="diesel"     <?php echo $carburant == 'diesel'     ? 'selected' : ''; ?>>Diesel</option>
            <option value="essence"    <?php echo $carburant == 'essence'    ? 'selected' : ''; ?>>Essence</option>
            <option value="hybride"    <?php echo $carburant == 'hybride'    ? 'selected' : ''; ?>>Hybride</option>
        </select>

        <button type="submit">Rechercher</button>
    </form>
</section>

<!-- Section liste des véhicules disponibles -->
<section class="voitures" id="voitures">
    <h2>Featured <span>Vehicles</span></h2>

    <div class="cards-grid">
        <?php if (count($voitures) == 0): ?>
            <!-- Aucun résultat trouvé avec les filtres appliqués -->
            <p style="color:#999;">Aucun véhicule trouvé.</p>

        <?php else: ?>
            <!-- Affichage d'une carte par véhicule -->
            <?php foreach ($voitures as $v): ?>
                <div class="card">
                    <!-- Image du véhicule, ou logo par défaut si pas d'image -->
                    <?php if ($v['image']): ?>
                        <img src="../assets/image/<?php echo $v['image']; ?>" class="card-img" alt="<?php echo $v['marque']; ?>">
                    <?php else: ?>
                        <img src="../assets/image/vw-logo.png" class="card-img" alt="VW">
                    <?php endif; ?>

                    <!-- Informations du véhicule -->
                    <div class="card-body">
                        <h3><?php echo $v['marque'] . ' ' . $v['modele']; ?></h3>
                        <p class="prix"><?php echo number_format($v['prix'], 0, ',', ' '); ?> DH</p>
                        <p>Carburant: <?php echo $v['carburant']; ?></p>
                        <p>Couleur: <?php echo $v['couleur']; ?></p>
                        <!-- Lien vers la page détail du véhicule -->
                        <a href="voiture.php?id=<?php echo $v['idVoiture']; ?>">Voir détails</a>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</section>

<!-- Section statistiques de la concession -->
<section class="stats">
    <div class="stat"><h3>1500+</h3><p>CARS SOLD</p></div>
    <div class="stat"><h3>50+</h3><p>LOCATIONS</p></div>
    <div class="stat"><h3>4.9/5</h3><p>CUSTOMER RATING</p></div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
