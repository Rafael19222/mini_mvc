<?php

// Active le mode strict pour la vérification des types
declare(strict_types=1);
// Déclare l'espace de noms pour ce contrôleur
namespace Mini\Controllers;
// Importe la classe de base Controller du noyau
use Mini\Core\Controller;
use Mini\Models\User;
use Mini\Models\Product;
use Mini\Models\Cart;

// Déclare la classe finale HomeController qui hérite de Controller
final class HomeController extends Controller
{
    // Déclare la méthode d'action par défaut qui ne retourne rien
    public function index(): void
    {
        // produit mis en avant les 4
        $featuredProducts = Product::getFeatured();
        
        $cartCount = 0;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $cart = new Cart($userId);
            $cartCount = $cart->getItemCount();
        }
        
        $this->render('home/index', params: [
            'title' => 'Bidoof - STORE',
            'featuredProducts' => $featuredProducts,
            'cartCount' => $cartCount
        ]);
    }

    public function users(): void
    {
        // Appelle le moteur de rendu avec la vue et ses paramètres
        $this->render('home/users', params: [
            // Définit le titre transmis à la vue
            'users' => $users = User::getAll(),
        ]);
    }
}