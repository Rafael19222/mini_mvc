<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? htmlspecialchars($title) : 'Bidoof Store' ?></title>
    <link rel="icon" type="image/png" href="https://www.pokepedia.fr/images/thumb/2/28/Keunotor-DP.png/800px-Keunotor-DP.png">
    <?php
        // Make asset paths work both with `-t public` and with router `public/index.php`
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\');
        if ($basePath === '.' || $basePath === '\\' || $basePath === '/') {
            $basePath = '';
        }
    ?>
    <!-- Primary stylesheet path based on current script location -->
    <link rel="stylesheet" href="<?= $basePath ?>/css/style.css?v=1.2">
    <!-- Fallbacks to handle both server modes in case the primary path fails -->
    <link rel="stylesheet" href="/css/style.css?v=1.2">
    <link rel="stylesheet" href="/public/css/style.css?v=1.2">
</head>
<body>
<?php
    $cartCount = 0;
    if (isset($_SESSION['user_id'])) {
        $cart = new \Mini\Models\Cart($_SESSION['user_id']);
        $cartCount = $cart->getItemCount();
    }
?>
<!-- Header style Fuji Store -->
<header>
    <div class="header-top">
        <div class="container">
            <a href="/" class="logo">
                <img src="https://www.pokepedia.fr/images/thumb/2/28/Keunotor-DP.png/800px-Keunotor-DP.png" alt="Logo" class="logo-img">
                BIDOOF STORE
            </a>
            <ul class="header-nav">
                <li><a href="/">Accueil</a></li>
            </ul>
            <div class="header-actions">
                <a class="cart-btn" href="/cart">Panier (<?= $cartCount ?>)</a>
                <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                    <span class="user-greeting">Bonjour <?= htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur') ?></span>
                    <a href="/logout" class="login-link">Déconnexion</a>
                <?php else: ?>
                    <a href="/login" class="login-link">Se connecter</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- Hero section -->
<?php if (!isset($hideHero) || !$hideHero): ?>
<section class="hero">
    <div class="hero-content">
        <div class="container">
            <h1>CARTES ET ACCESSOIRES POKÉMON À COLLECTIONNER</h1>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contenu principal -->
<main>
    <?= $content ?>
</main>

</body>
</html>

