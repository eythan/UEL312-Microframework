<?php declare(strict_types=1);

namespace Framework312\Router;

use Framework312\Router\Exception as RouterException;
use Framework312\Template\Renderer;
use Symfony\Component\HttpFoundation\Response;

class Route {
    private const VIEW_CLASS = 'Framework312\Router\View\BaseView';
    private const VIEW_USE_TEMPLATE_FUNC = 'use_template';
    private const VIEW_RENDER_FUNC = 'render';

    private string $view;

    public function __construct(string|object $class_or_view) {
        $reflect = new \ReflectionClass($class_or_view);
        $view = $reflect->getName();
        if (!$reflect->isSubclassOf(self::VIEW_CLASS)) {
            throw new RouterException\InvalidViewImplementation($view);
        }
        $this->view = $view;
    }

    public function call(Request $request, ?Renderer $engine): Response {
	    // TODO
    }
}

class SimpleRouter implements Router {
    private Renderer $engine;

    public function __construct(Renderer $engine) {
        $this->engine = $engine;
        // TODO
    }

    public function register(string $path, string|object $class_or_view) {
	    // TODO
    }

    public function serve(mixed ...$args): void {
        // Recuperation de la requête HTTP
        $request = Request::createFromGlobals();

        // Recuperation de l'url demandé
        $path = rtrim($request->getPathInfo(), '/') ?: '/';

        $matched = null;
        $matches = [];

        // Boucle dans un tableau de route pour trouvé le correspondant
        foreach ($this->routes as $item) {
            if (preg_match($item['regex'], $path, $matches)) {
                $matched = $item['route'];
                break;
            }
        }

        // Si aucune route -> Erreur 404
        if (!$matched) {
            $response = new Response("404 Not Found", Response::HTTP_NOT_FOUND);
            $response->send();
            return;
        }

        // Récupération des parametre de l'URL
        foreach ($matches as $key => $value) {
            if (!is_numeric($key)) {
                $request->attributes->set($key, $value);
            }
        }

        $response = $matched->call($request, $this->engine);
        $response->send();
    }
}