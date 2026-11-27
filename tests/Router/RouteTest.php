<?php declare(strict_types=1);

namespace Framework312\Tests\Router;

use Framework312\Router\Request;
use Framework312\Router\SimpleRouter;
use PHPUnit\Framework\TestCase;
use Framework312\Router\Exception\InvalidViewImplementation;
use Framework312\Router\Route;
use Framework312\Router\View\HTMLView;
use Framework312\Router\View\TemplateView;
use \Framework312\Template\Renderer;


class_exists(SimpleRouter::class);

final class RouteTest extends TestCase
{   // Test le comportement du constructeur lorsque la classe hérite de BaseView
    public function testConstructeur1(): void
    {
        $view = new class extends HTMLView {
            protected function get(Request $request): string
            {
                return '<h1>Test</h1>';
            }
        };

        $route = new Route($view);

        $this->assertInstanceOf(Route::class, $route);
    }
    // Test le comportement du constructeur lorsque la classe n'hérite pas de BaseView
    public function testConstructeur2(): void
    {
        $view = new class {
            public function test(): string
            {
                return '<h1>Test</h1>';
            }
        };

        $this->expectException(InvalidViewImplementation::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('does not extend Framework312\Router\View\BaseView');

        new Route($view);
    }
    // Test le comportement de call() lorsque la View n'utilise pas d'engine (renderer)
    public function testCall1(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $view = new class extends HTMLView {
            public static mixed $engineRecu;

            public function __construct($engine = null)
            {
                self::$engineRecu = $engine;
            }

            protected function get(Request $request): string
            {
                return '<h1>Test</h1>';
            }
        };

        $route = new Route($view);
        $engine = $this->createMock(Renderer::class);
        $response = $route->call(new Request(), $engine);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('<h1>Test</h1>', $response->getContent());
        $this->assertNull($view::$engineRecu);
    }
    // Test le comportement de call() lorsque la View utilise un engine (renderer)
    public function testCall2(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $engine = $this->createMock(Renderer::class);
        $engine->method('render')->willReturn('<h1>Test</h1>');

        $view = new class($engine) extends TemplateView {
            public static mixed $engineRecu = null;

            public function __construct(Renderer $engine)
            {
                parent::__construct($engine);
                self::$engineRecu = $engine;
            }

            protected function get(Request $request): array
            {
                return ['template' => 'test.twig', 'data' => []];
            }
        };

        $view::$engineRecu = null;   // efface la trace laissée par notre propre "new"

        $route = new Route($view);
        $response = $route->call(new Request(), $engine);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('<h1>Test</h1>', $response->getContent());
        $this->assertSame($engine, $view::$engineRecu);
    }
}