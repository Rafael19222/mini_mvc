<?php
// Active le mode strict pour les types
declare(strict_types=1);
// Espace de noms du noyau
namespace Mini\Core;
// Déclare le routeur HTTP avec support des paramètres dynamiques
final class Router
{
    // Tableau des routes : [méthode, chemin, [ClasseContrôleur, action]]
    /** @var array<int, array{0:string,1:string,2:array{0:class-string,1:string}} > */
    private array $routes;

    /**
     * Initialise le routeur avec les routes configurées
     * @param array<int, array{0:string,1:string,2:array{0:class-string,1:string}} > $routes
     */
    public function __construct(array $routes)
    {
        // Mémorise les routes fournies
        $this->routes = $routes;
    }

    // Dirige la requête vers le bon contrôleur en fonction méthode/URI
    public function dispatch(string $method, string $uri): void
    {
        // Extrait uniquement le chemin de l'URI
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Parcourt chaque route enregistrée
        foreach ($this->routes as [$routeMethod, $routePath, $handler]) {
            // Vérifie correspondance de méthode
            if ($method !== $routeMethod) {
                continue;
            }

            // Vérifie correspondance exacte d'abord (pour les routes sans paramètres)
            if ($path === $routePath) {
                $this->executeRoute($handler);
                return;
            }

            // Vérifie les routes avec paramètres dynamiques
            $params = $this->matchRoute($routePath, $path);
            if ($params !== null) {
                // Met les paramètres dans $_GET pour les contrôleurs
                $_GET = array_merge($_GET, $params);
                $this->executeRoute($handler);
                return;
            }
        }

        // Si aucune route ne correspond, renvoie un 404 minimaliste
        http_response_code(404);
        echo '404 Not Found';
    }

    /**
     * Vérifie si un chemin correspond à une route avec paramètres
     * @param string $routePath Chemin de la route avec paramètres (ex: "/product/{id}")
     * @param string $requestPath Chemin de la requête (ex: "/product/123")
     * @return array<string, string>|null Paramètres extraits ou null si pas de correspondance
     */
    private function matchRoute(string $routePath, string $requestPath): ?array
    {
        // Convertit les paramètres {name} en regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        // Vérifie si le chemin correspond
        if (!preg_match($pattern, $requestPath, $matches)) {
            return null;
        }

        // Extrait les noms des paramètres
        preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames);
        
        $params = [];
        for ($i = 1; $i < count($matches); $i++) {
            $paramName = $paramNames[1][$i - 1] ?? "param$i";
            $params[$paramName] = $matches[$i];
        }

        return $params;
    }

    /**
     * Exécute une route
     * @param array{0:class-string,1:string} $handler
     */
    private function executeRoute(array $handler): void
    {
        // Déstructure le gestionnaire en [classe, action]
        [$class, $action] = $handler;
        // Instancie le contrôleur cible
        $controller = new $class();
        // Appelle l'action sur le contrôleur
        $controller->$action();
    }
}


