<?php
session_start();
require 'config/db.php';

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Vérifier si c'est un client
    $stmt = $pdo->prepare("SELECT * FROM client WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['motPasse'])) {
        // Connecter le client et rediriger vers son espace
        $_SESSION['client'] = $user['idClient'];
        header('Location: client/home.php');
        exit;
    }

    // Vérifier si c'est un administrateur
    $stmt = $pdo->prepare("SELECT * FROM administrateur WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['motPasse'])) {
        // Connecter l'admin et rediriger vers le tableau de bord
        $_SESSION['admin'] = $admin['idAdmin'];
        header('Location: admin/dashboard.php');
        exit;
    }

    // Aucune correspondance trouvée
    $erreur = "Email ou mot de passe incorrect";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AutoVent - Connexion</title>
    <style>

        /* Réinitialisation générale */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }

        body { display: flex; height: 100vh; }

        /* Partie gauche - image Volkswagen */
        .gauche {
            width: 55%;
            background-color: #001E50;
            overflow: hidden;
        }

        .gauche img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Partie droite - formulaire de connexion */
        .droite {
            width: 45%;
            background: #f2f2f2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        /* Conteneur principal du formulaire */
        .boite {
            width: 100%;
            max-width: 380px;
        }

        /* Logo en haut du formulaire */
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .logo img { width: 40px; }

        .logo span {
            font-size: 22px;
            font-weight: bold;
            color: #001E50;
        }

        .logo span b { color: #D60000; }

        /* Titre et sous-titre */
        h2 { font-size: 28px; color: #001E50; margin-bottom: 8px; }

        .sous-titre { color: #666; font-size: 14px; margin-bottom: 25px; }

        /* Chaque champ du formulaire */
        .champ { margin-bottom: 18px; }

        .champ label {
            display: block;
            font-size: 13px;
            color: #333;
            margin-bottom: 6px;
        }

        .champ input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            background: white;
            outline: none;
        }

        /* Bordure bleue au focus */
        .champ input:focus { border-color: #001E50; }

        /* Bouton de connexion */
        .btn-connexion {
            width: 100%;
            padding: 13px;
            background: #D60000;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        /* Hover du bouton - passe au bleu VW */
        .btn-connexion:hover { background: #001E50; }

        /* Lien vers la page d'inscription */
        .lien-inscription {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .lien-inscription a { color: #D60000; text-decoration: none; font-weight: bold; }

        /* Message d'erreur */
        .erreur {
            background: #ffe0e0;
            color: #D60000;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }

    </style>
</head>
<body>

    <!-- Image Volkswagen à gauche -->
    <div class="gauche">
        <img src="assets/image/vw-logo.png" alt="Volkswagen">
    </div>

    <!-- Formulaire de connexion à droite -->
    <div class="droite">
        <div class="boite">

            <!-- Logo  -->
            <div class="logo">
                <img src="assets/image/logo.png" alt="VW">
                <span>Auto<b>Vente</b></span>
            </div>

            <h2>Bienvenue</h2>
            <p class="sous-titre">Entrez vos identifiants pour accéder à AutoVent.</p>

            <!-- Affichage du message d'erreur  -->
            <?php if (isset($erreur)) echo "<p class='erreur'>$erreur</p>"; ?>

            <form method="POST" action="login.php">

                <!-- Champ email -->
                <div class="champ">
                    <label>Adresse email</label>
                    <input type="email" name="email" placeholder="name@autovent.com" required>
                </div>

                <!-- Champ mot de passe -->
                <div class="champ">
                    <label>Mot de passe</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <!-- Bouton de soumission -->
                <button type="submit" class="btn-connexion">Se connecter →</button>

            </form>

            <!-- Lien vers la page d'inscription -->
            <div class="lien-inscription">
                Pas encore de compte ? <a href="signup.php">S'inscrire</a>
            </div>

        </div>
    </div>

</body>
</html>