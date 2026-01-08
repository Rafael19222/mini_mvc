<?php

namespace Mini\Controllers;

use Mini\Core\Controller;
use Mini\Models\Cart;

class CartController extends Controller
{
    private Cart $cart;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }
        $this->cart = new Cart($userId);
    }

    public function index(): void
    {
        $this->render('cart/index', [
            'title' => 'Panier',
            'hideHero' => true,
            'items' => $this->cart->getItems(),
            'total' => $this->cart->getTotal(),
            'formattedTotal' => $this->cart->getFormattedTotal(),
            'itemCount' => $this->cart->getItemCount()
        ]);
    }

    public function add(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.0 405 Method Not Allowed');
            exit;
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (!$productId || $quantity <= 0) {
            $_SESSION['flash_error'] = 'Données invalides';
            header('Location: /cart');
            exit;
        }

        try {
            $this->cart->addItem($productId, $quantity);
            $_SESSION['flash_success'] = 'Produit ajouté au panier';
            header('Location: /cart');
            exit;
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: /cart');
            exit;
        }
    }

    public function remove(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.0 405 Method Not Allowed');
            exit;
        }

        $productId = (int)($_POST['product_id'] ?? 0);

        if (!$productId) {
            $_SESSION['flash_error'] = 'Produit non trouvé';
            header('Location: /cart');
            exit;
        }

        $this->cart->removeItem($productId);
        $_SESSION['flash_success'] = 'Produit retiré du panier';
        header('Location: /cart');
        exit;
    }

    public function clear(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.0 405 Method Not Allowed');
            exit;
        }

        $this->cart->clear();
        $_SESSION['flash_success'] = 'Panier vidé';
        header('Location: /cart');
        exit;
    }
}