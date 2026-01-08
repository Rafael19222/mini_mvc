<!-- Section produits vedettes -->
<section class="featured-products">
    <div class="container">
        <h2>Produits vedettes</h2>
        <div class="products-grid">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="<?= $product->getImage() ?>" alt="<?= htmlspecialchars($product->getName()) ?>" />
                    <?php if ($product->isFeatured()): ?>
                    <?php endif; ?>
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
        
        <div class="text-center text-center-section">
            <a href="/products" class="btn btn-outline">Voir tous les produits</a>
        </div>
    </div>
</section>


