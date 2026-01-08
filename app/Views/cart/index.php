<?php
/** @var array $items */
/** @var string $formattedTotal */
/** @var int $itemCount */
?>

<section class="cart-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Votre panier</h2>
            <?php if ($itemCount > 0): ?>
                <p class="section-subtitle"><?= $itemCount ?> article<?= $itemCount > 1 ? 's' : '' ?></p>
            <?php endif; ?>
        </div>

        <?php if (empty($items)): ?>
            <div class="cart-empty">
                <p>Votre panier est vide.</p>
                <a class="btn btn-primary" href="/products">Voir les produits</a>
            </div>
        <?php else: ?>
            <div class="cart-content">
                <div class="cart-items">
                    <div class="cart-table">
                        <?php foreach ($items as $item): ?>
                            <?php $product = $item['product']; ?>
                            <div class="cart-row">
                                <div class="cart-col cart-product">
                                    <div class="cart-product-info">
                                        <img src="<?= htmlspecialchars($product->getImage()) ?>" alt="<?= htmlspecialchars($product->getName()) ?>">
                                        <div>
                                            <div class="cart-product-name"><?= htmlspecialchars($product->getName()) ?></div>
                                            <div class="cart-product-cat"><?= htmlspecialchars($product->getCategory()) ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cart-col cart-total">
                                    <strong><?= number_format($item['price'] * $item['quantity'], 2, ',', ' ') ?> €</strong>
                                </div>
                                <div class="cart-col cart-actions">
                                    <form method="POST" action="/cart/remove">
                                        <input type="hidden" name="product_id" value="<?= $product->getId() ?>">
                                        <button type="submit" class="btn btn-danger">supprimer</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="cart-sidebar">
                    <div class="cart-summary">
                        <h3 class="summary-title">Récapitulatif</h3>
                        <div class="summary-line">
                            <span>Sous-total (<?= $itemCount ?> article<?= $itemCount > 1 ? 's' : '' ?>)</span>
                            <span><?= $formattedTotal ?></span>
                        </div>
                        <div class="summary-line">
                            <strong>Total</strong>
                            <strong><?= $formattedTotal ?></strong>
                        </div>
                        <div class="summary-actions">
                            <a class="btn btn-primary" href="#">Commander</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
