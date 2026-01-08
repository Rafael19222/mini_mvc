<?php

declare(strict_types=1);

namespace Mini\Controllers;

use Mini\Core\Controller;
use Mini\Models\Cart;
use Mini\Models\Order;
use Mini\Models\User;

class OrderController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    public function checkout(): void
    {
        $this->requireLogin();
        
        $cart = new Cart();
        
        if ($cart->isEmpty()) {
            $_SESSION['flash_error'] = 'Votre panier est vide';
            header('Location: /cart');
            exit;
        }

        if (!$cart->validateStock()) {
            $_SESSION['flash_error'] = 'Certains articles ne sont plus disponibles en quantité suffisante';
            header('Location: /cart');
            exit;
        }

        $user = User::getById($_SESSION['user_id']);
        
        $this->render('orders/checkout', [
            'title' => 'Finaliser ma commande',
            'cart' => $cart,
            'user' => $user,
            'items' => $cart->getItems(),
            'total' => $cart->getTotal(),
            'formattedTotal' => $cart->getFormattedTotal()
        ]);
    }

    public function process(): void
    {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /checkout');
            exit;
        }

        $cart = new Cart();
        
        if ($cart->isEmpty()) {
            $_SESSION['flash_error'] = 'Votre panier est vide';
            header('Location: /cart');
            exit;
        }

        // Récupérer les données du formulaire
        $shippingAddress = $this->sanitizeAddress([
            'name' => $_POST['shipping_name'] ?? '',
            'address' => $_POST['shipping_address'] ?? '',
            'city' => $_POST['shipping_city'] ?? '',
            'postal_code' => $_POST['shipping_postal_code'] ?? '',
            'country' => $_POST['shipping_country'] ?? 'France'
        ]);

        $useSameAddress = isset($_POST['same_address']);
        
        $billingAddress = $useSameAddress ? $shippingAddress : $this->sanitizeAddress([
            'name' => $_POST['billing_name'] ?? '',
            'address' => $_POST['billing_address'] ?? '',
            'city' => $_POST['billing_city'] ?? '',
            'postal_code' => $_POST['billing_postal_code'] ?? '',
            'country' => $_POST['billing_country'] ?? 'France'
        ]);

        $errors = [];

        // Validation adresse de livraison
        $errors = array_merge($errors, $this->validateAddress($shippingAddress, 'livraison'));

        // Validation adresse de facturation si différente
        if (!$useSameAddress) {
            $errors = array_merge($errors, $this->validateAddress($billingAddress, 'facturation'));
        }

        if (!empty($errors)) {
            $user = User::getById($_SESSION['user_id']);
            $this->render('orders/checkout', [
                'title' => 'Finaliser ma commande',
                'cart' => $cart,
                'user' => $user,
                'items' => $cart->getItems(),
                'total' => $cart->getTotal(),
                'formattedTotal' => $cart->getFormattedTotal(),
                'errors' => $errors,
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
                'same_address' => $useSameAddress
            ]);
            return;
        }

        try {
            // Créer la commande
            $order = Order::createFromCart(
                $cart,
                $_SESSION['user_id'],
                $this->formatAddress($shippingAddress),
                $this->formatAddress($billingAddress)
            );

           

            // Rediriger vers la confirmation
            $_SESSION['flash_success'] = 'Votre commande a été créée avec succès !';
            header('Location: /order/' . $order->getId() . '/confirmation');
            exit;

        } catch (\Exception $e) {
            $user = User::getById($_SESSION['user_id']);
            $this->render('orders/checkout', [
                'title' => 'Finaliser ma commande',
                'cart' => $cart,
                'user' => $user,
                'items' => $cart->getItems(),
                'total' => $cart->getTotal(),
                'formattedTotal' => $cart->getFormattedTotal(),
                'errors' => ['Erreur lors de la création de la commande : ' . $e->getMessage()],
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
                'same_address' => $useSameAddress
            ]);
        }
    }

    public function confirmation(): void
    {
        $this->requireLogin();
        
        $orderId = (int)($_GET['id'] ?? 0);
        
        if (!$orderId) {
            header('HTTP/1.0 404 Not Found');
            $this->render('errors/404');
            return;
        }

        $order = Order::getById($orderId);
        
        if (!$order || $order->getUserId() !== $_SESSION['user_id']) {
            header('HTTP/1.0 404 Not Found');
            $this->render('errors/404');
            return;
        }

        $this->render('orders/confirmation', [
            'title' => 'Confirmation de commande',
            'order' => $order
        ]);
    }

    public function show(): void
    {
        $this->requireLogin();
        
        $orderId = (int)($_GET['id'] ?? 0);
        
        if (!$orderId) {
            header('HTTP/1.0 404 Not Found');
            $this->render('errors/404');
            return;
        }

        $order = Order::getById($orderId);
        
        if (!$order || $order->getUserId() !== $_SESSION['user_id']) {
            header('HTTP/1.0 404 Not Found');
            $this->render('errors/404');
            return;
        }

        $this->render('orders/show', [
            'title' => 'Commande #' . $order->getId(),
            'order' => $order
        ]);
    }

    public function history(): void
    {
        $this->requireLogin();
        
        $orders = Order::getByUserId($_SESSION['user_id']);
        
        // Trier par date de création décroissante
        usort($orders, function($a, $b) {
            return strcmp($b->getCreatedAt(), $a->getCreatedAt());
        });

        $this->render('orders/history', [
            'title' => 'Mes commandes',
            'orders' => $orders
        ]);
    }

    public function cancel(): void
    {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.0 405 Method Not Allowed');
            exit;
        }

        $orderId = (int)($_POST['order_id'] ?? 0);
        
        if (!$orderId) {
            $_SESSION['flash_error'] = 'Commande non trouvée';
            header('Location: /orders');
            exit;
        }

        $order = Order::getById($orderId);
        
        if (!$order || $order->getUserId() !== $_SESSION['user_id']) {
            $_SESSION['flash_error'] = 'Commande non trouvée';
            header('Location: /orders');
            exit;
        }

        try {
            $order->cancel();
            $_SESSION['flash_success'] = 'Commande annulée avec succès';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
        }

        header('Location: /order/' . $orderId);
        exit;
    }

    private function requireLogin(): void
    {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $_SESSION['flash_error'] = 'Vous devez être connecté pour accéder à cette page';
            header('Location: /login');
            exit;
        }
    }

    private function sanitizeAddress(array $address): array
    {
        return array_map(function($value) {
            return trim(htmlspecialchars($value));
        }, $address);
    }

    private function validateAddress(array $address, string $type): array
    {
        $errors = [];
        
        if (empty($address['name'])) {
            $errors[] = "Le nom pour l'adresse de {$type} est obligatoire";
        }
        
        if (empty($address['address'])) {
            $errors[] = "L'adresse de {$type} est obligatoire";
        }
        
        if (empty($address['city'])) {
            $errors[] = "La ville pour l'adresse de {$type} est obligatoire";
        }
        
        if (empty($address['postal_code'])) {
            $errors[] = "Le code postal pour l'adresse de {$type} est obligatoire";
        } elseif (!preg_match('/^\d{5}$/', $address['postal_code'])) {
            $errors[] = "Le code postal pour l'adresse de {$type} doit contenir 5 chiffres";
        }
        
        return $errors;
    }

    private function formatAddress(array $address): string
    {
        return implode("\n", [
            $address['name'],
            $address['address'],
            $address['postal_code'] . ' ' . $address['city'],
            $address['country']
        ]);
    }
}