<?php declare(strict_types=1);

namespace Framework312\Tests\Router\View;

use Framework312\Router\Request;
use Framework312\Router\View\HTMLView;
use Framework312\Router\Exception\HttpMethodNotImplemented;
use PHPUnit\Framework\TestCase;

final class HTMLViewTest extends TestCase
{
    public function testUseTemplateRenvoieFalse(): void
    {
        $this->assertFalse(HTMLView::use_template());
    }
    // Teste que render() appelle la méthode correspondant au verbe HTTP et renvoie du HTML
    public function testRender1(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $view = new class extends HTMLView {
            protected function get(Request $request): string {
                return '<h1>Test</h1>';
            }
        };

        $response = $view->render(new Request());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('<h1>Test</h1>', $response->getContent());
        $this->assertStringContainsString('text/html', $response->headers->get('content-type'));
    }
    // Teste que render() lance une exception si le résultat de la méthode n'est pas du HTML
    public function testRender2(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $view = new class extends HTMLView {
            protected function post(Request $request): array {
                return [1, 2, 3];
            }
        };

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Le content doit être de type string');

        $view->render(new Request());
    }

    // Teste que render() refuse un verbe non prévu (ex : HEAD) au lieu d'appeler une méthode inexistante
    public function testRenderVerbeNonAutorise(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'HEAD';

        $view = new class extends HTMLView {};

        $this->expectException(HttpMethodNotImplemented::class);
        $this->expectExceptionCode(405);

        $view->render(new Request());
    }

    // Teste qu'un en-tête X-HTTP-Method-Override ne permet pas d'appeler render() (boucle infinie)
    public function testRenderRefuseLOverrideVersRender(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'RENDER';

        $view = new class extends HTMLView {};

        $this->expectException(HttpMethodNotImplemented::class);

        $view->render(new Request());
    }

    // Nettoie l'en-tête simulé pour ne pas perturber les autres tests
    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
    }
}
