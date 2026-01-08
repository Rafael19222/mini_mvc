<section class="product-detail">
    <div class="container">
        <div class="product-detail-wrapper">
            <div class="product-detail-image">
                <img src="<?= $product->getImage() ?>" alt="<?= htmlspecialchars($product->getName()) ?>" />
            </div>
            
            <div class="product-detail-info">
                <h1><?= htmlspecialchars($product->getName()) ?></h1>
                
                <div class="product-meta">
                    <span class="category"><?= htmlspecialchars($product->getCategory()) ?></span>
                    <?php if ($product->isFeatured()): ?>
                        <span class="badge-featured">Vedette</span>
                    <?php endif; ?>
                </div>

                <div class="product-price-section">
                    <span class="price"><?= $product->getFormattedPrice() ?></span>
                  

                <div class="product-description">
                    <h3>Description</h3>
                    <p><?= nl2br(htmlspecialchars($product->getDescription())) ?></p>
                </div>

                <div class="product-actions-detail">
                    <form method="POST" action="/cart/add" class="add-to-cart-form">
                        <input type="hidden" name="product_id" value="<?= $product->getId() ?>" />
                        
                        <div class="quantity-selector">
                            <label for="quantity">Quantité :</label>
                            <select id="quantity" name="quantity" class="form-control">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg">
                            Ajouter au panier
                        </button>
                    </form>
                    
                    <button class="btn btn-outline btn-lg" onclick="history.back()">
                        ← Retour
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
