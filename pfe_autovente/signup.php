<?php
session_start();
require 'config/db.php';

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom       = $_POST['nom'];
    $prenom    = $_POST['prenom'];
    $email     = $_POST['email'];
    $telephone = $_POST['telephone'];
    $adresse   = $_POST['adresse'];
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // Insérer le nouveau client dans la base de données
        $stmt = $pdo->prepare("INSERT INTO client (nom, prenom, email, motPasse, telephone, adresse) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $password, $telephone, $adresse]);

        // Rediriger vers la page de connexion après inscription
        header('Location: login.php');
        exit;

    } catch (PDOException $e) {
        $erreur = "Erreur : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AutoVent - Inscription</title>
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

        /* Partie droite - formulaire d'inscription */
        .droite {
            width: 45%;
            background: #f2f2f2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            overflow-y: auto;
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

        .logo span { font-size: 22px; font-weight: bold; color: #001E50; }

        .logo span b { color: #D60000; }

        /* Titre et sous-titre */
        h2 { font-size: 28px; color: #001E50; margin-bottom: 8px; }

        .sous-titre { color: #666; font-size: 14px; margin-bottom: 25px; }

        /* Chaque champ du formulaire */
        .champ { margin-bottom: 15px; }

        .champ label { display: block; font-size: 13px; color: #333; margin-bottom: 6px; }

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

        /* Bouton d'inscription */
        .btn-inscrire {
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
        .btn-inscrire:hover { background: #001E50; }

        /* Lien vers la page de connexion */
        .lien-login { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }

        .lien-login a { color: #D60000; text-decoration: none; font-weight: bold; }

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

    <!-- Formulaire d'inscription à droite -->
    <div class="droite">
        <div class="boite">

            <!-- Logo  -->
            <div class="logo">
                <img src="assets/image/logo.png" alt="VW">
            </div>

            <h2>Créer un compte</h2>
            <p class="sous-titre">Remplissez le formulaire pour vous inscrire.</p>

            <!-- Affichage du message d'erreur-->
            <?php if (isset($erreur)) echo "<p class='erreur'>$erreur</p>"; ?>

            <form method="POST" action="signup.php">

                <!-- Champ nom -->
                <div class="champ">
                    <label>Nom</label>
                    <input type="text" name="nom" placeholder="Votre nom" required>
                </div>

                <!-- Champ prénom -->
                <div class="champ">
                    <label>Prénom</label>
                    <input type="text" name="prenom" placeholder="Votre prénom" required>
                </div>

                <!-- Champ email -->
                <div class="champ">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="name@gmail.com" required>
                </div>

                <!-- Champ mot de passe -->
                <div class="champ">
                    <label>Mot de passe</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <!-- Champ téléphone -->
                <div class="champ">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" placeholder="06xxxxxxxx">
                </div>

                <!-- Champ adresse -->
                <div class="champ">
                    <label>Adresse</label>
                    <input type="text" name="adresse" placeholder="Votre adresse">
                </div>

                <!-- Bouton de soumission -->
                <button type="submit" class="btn-inscrire">S'inscrire →</button>

            </form>

            <!-- Lien vers la page de connexion -->
            <div class="lien-login">
                Déjà inscrit ? <a href="login.php">Se connecter</a>
            </div>

        </div>
    </div>

</body>
</html>