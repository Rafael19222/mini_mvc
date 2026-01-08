<section class="product-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?= htmlspecialchars($title ?? 'Tous les produits') ?></h2>
        </div>

        <?php if (!empty($products)): ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="<?= $product->getImage() ?>" alt="<?= htmlspecialchars($product->getName()) ?>" />
                </div>
                <div class="product-info">
                    <h3><?= htmlspecialchars($product->getName()) ?></h3>
                    <p class="product-category"><?= htmlspecialchars($product->getCategory()) ?></p>
                    <p class="product-price"><?= $product->getFormattedPrice() ?></p>
                    <div class="product-actions">
                        <a href="/product/<?= $product->getId() ?>" class="btn btn-secondary">Voir détails</a>
                        <form class="add-to-cart-form" method="POST" action="/cart/add">
                            <input type="hidden" name="product_id" value="<?= $product->getId() ?>" />
                            <input type="hidden" name="quantity" value="1" />
                            <button type="submit" class="btn btn-primary">Ajouter au panier</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <p>Aucun produit trouvé.</p>
        <?php endif; ?>
    </div>
    <br>
</section>
