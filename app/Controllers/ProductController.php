<?php

declare(strict_types=1);

namespace Mini\Controllers;

use Mini\Core\Controller;
use Mini\Models\Product;

class ProductController extends Controller
{
    public function index(): void
    {
        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;
        
        if ($search) {
            $products = Product::search($search);
            $title = 'Résultats pour "' . htmlspecialchars($search) . '"';
        } elseif ($category) {
            $products = Product::getByCategory($category);
            $title = 'Catégorie : ' . htmlspecialchars($category);
        } else {
            $products = Product::getAll();
            $title = 'Tous les produits';
        }

        $this->render('products/index', [
            'products' => $products,
            'title' => $title,
            'currentCategory' => $category,
            'searchQuery' => $search
        ]);
    }

    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        
        if (!$id) {
            header('HTTP/1.0 404 Not Found');
            $this->render('errors/404');
            return;
        }

        $product = Product::getById($id);
        
        if (!$product) {
            header('HTTP/1.0 404 Not Found');
            $this->render('errors/404');
            return;
        }

        // Produits suggérés de la même catégorie
        $suggestedProducts = array_filter(
            Product::getByCategory($product->getCategory()),
            fn($p) => $p->getId() !== $product->getId()
        );
        $suggestedProducts = array_slice($suggestedProducts, 0, 4);

        $this->render('products/show', [
            'product' => $product,
            'suggestedProducts' => $suggestedProducts,
            'hideHero' => true
        ]);
    }

    public function featured(): void
    {
        $featuredProducts = Product::getFeatured();
        
        $this->render('products/featured', [
            'products' => $featuredProducts,
            'title' => 'Produits vedettes'
        ]);
    }

    public function category(): void
    {
        $category = $_GET['category'] ?? '';
        
        if (!$category) {
            header('Location: /products');
            exit;
        }

        $products = Product::getByCategory($category);
        $title = 'Catégorie : ' . ucfirst($category);

        $this->render('products/category', [
            'products' => $products,
            'title' => $title,
            'category' => $category
        ]);
    }

    public function search(): void
    {
        $query = $_GET['q'] ?? $_POST['q'] ?? '';
        
        if (!$query) {
            $products = [];
            $title = 'Recherche';
        } else {
            $products = Product::search($query);
            $title = count($products) . ' résultat(s) pour "' . htmlspecialchars($query) . '"';
        }

        $this->render('products/search', [
            'products' => $products,
            'title' => $title,
            'query' => $query
        ]);
    }

    public function ajax_search(): void
    {
        header('Content-Type: application/json');
        
        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 2) {
            echo json_encode([]);
            exit;
        }

        $products = Product::search($query);
        $results = [];

        foreach ($products as $product) {
            $results[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getFormattedPrice(),
                'image' => $product->getImage(),
                'url' => '/product/' . $product->getId()
            ];
        }

        echo json_encode($results);
        exit;
    }
}