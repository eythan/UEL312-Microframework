<?php declare(strict_types=1);

namespace Framework312\Router;

use Framework312\Router\Exception as RouterException;
use Framework312\Template\Renderer;
use Symfony\Component\HttpFoundation\Response;
use Framework312\Router\View\TemplateView;

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
        $viewClass = $this->view;

        // Instanciation et transmission au moteur de template
        if (is_subclass_of($viewClass, TemplateView::class) && $engine !== null) {
            $view = new $viewClass($engine);
        } else {
            $view = new $viewClass();
        }

        return $view->render($request);
    }
}

class SimpleRouter implements Router {
    private Renderer $engine;
    private array $routes = [];

    public function __construct(Renderer $engine) {
        $this->engine = $engine;
    }

    public function register(string $path, string|object $class_or_view) {
        // Transforme les variables en regex pour lire leur valeur
        $regex = preg_replace('#:([a-zA-Z0-9_]+)#', '(?P<$1>[^/]+)', $path);

        $this->routes[] = [
            'regex' => '#^' . $regex . '$#',
            'route' => new Route($class_or_view),
        ];
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