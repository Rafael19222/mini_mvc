<div class="container auth-container">
    <h2>Connexion</h2>

    <?php if ($error): ?>
        <div class="error-message">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group-last">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="auth-submit-btn">
            Se connecter
        </button>
    </form>

    <p class="auth-footer">
        Pas de compte ? <a href="/register">S'inscrire</a>
    </p>
</div>
