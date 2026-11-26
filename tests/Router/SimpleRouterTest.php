<?php declare(strict_types=1);

namespace Framework312\Tests\Router;

use Framework312\Router\Exception\HttpMethodNotImplemented;
use Framework312\Router\Exception\InvalidViewImplementation;
use Framework312\Router\Request;
use Framework312\Router\SimpleRouter;
use Framework312\Router\View\HTMLView;
use Framework312\Router\View\JSONView;
use Framework312\Template\Renderer;
use PHPUnit\Framework\TestCase;

final class SimpleRouterTest extends TestCase
{
    private SimpleRouter $router;

    //------- Préparation -------

    // Exécutée avant chaque test : un router et une requête par défaut
    protected function setUp(): void
    {
        $this->router = new SimpleRouter($this->createMock(Renderer::class));
        $this->simulerRequete('GET', '/');
    }

    // Remplit $_SERVER pour simuler une requête HTTP
    private function simulerRequete(string $methode, string $uri): void
    {
        $_SERVER['REQUEST_METHOD']  = $methode;
        $_SERVER['REQUEST_URI']     = $uri;
        $_SERVER['SCRIPT_NAME']     = '/index.php';
        $_SERVER['SCRIPT_FILENAME'] = '/index.php';
        $_SERVER['PHP_SELF']        = '/index.php';
    }

    // Une View HTML qui affiche l'id extrait de l'URL
    private function viewLivre(): HTMLView
    {
        return new class extends HTMLView {
            protected function get(Request $request): string
            {
                return 'Livre ' . $request->attributes->get('id');
            }
        };
    }

    //------- Test -------
    // Test le comportement de register() lorsque la classe n'hérite pas de BaseView
    public function testRegister1(): void
    {
        $this->expectException(InvalidViewImplementation::class);

        $this->router->register('/book/:id', \stdClass::class);
    }
    // Test que Serve() appelle la bonne View pour un chemin fixe (sans paramètre)
    public function testServe1(): void
    {
        $this->router->register('/accueil', new class extends HTMLView {
            protected function get(Request $request): string
            {
                return 'Bienvenue';
            }
        });
        $this->simulerRequete('GET', '/accueil');

        $this->expectOutputString('Bienvenue');
        $this->router->serve();
    }
    // Test que Serve() fonctionne avec un identifiant dans l'URL et que la View reçoit bien la valeur du paramètre
    public function testServe2(): void
    {
        $this->router->register('/book/:id', $this->viewLivre());
        $this->simulerRequete('GET', '/book/7ABd9x');

        $this->expectOutputString('Livre 7ABd9x');
        $this->router->serve();
    }
    // Test le comportement de Serve() lorsqu'aucune route ne correspond à l'URL demandée
    public function testServe3(): void
    {
        $this->router->register('/book/:id', $this->viewLivre());
        $this->simulerRequete('GET', '/auteur/12');

        $this->expectOutputString('404 Not Found');
        $this->router->serve();
    }
}