<?php declare(strict_types=1);

namespace Framework312\Tests\Router\View;

use Framework312\Router\Request;
use Framework312\Router\View\JSONView;
use PHPUnit\Framework\TestCase;

final class JSONViewTest extends TestCase
{
    public function testUseTemplateRenvoieFalse(): void
    {
        $this->assertFalse(JSONView::use_template());
    }
    // Teste que render() appelle la méthode correspondant au verbe HTTP, encode les données en JSON et renvoie le JSON
    public function testRender1(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $view = new class extends JSONView {
            protected function get(Request $request): array {
                return ['message' => 'Test'];
            }
        };

        $response = $view->render(new Request());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"message":"Test"}', $response->getContent());
        $this->assertStringContainsString('application/json', $response->headers->get('content-type'));
    }
    // Teste que render() lance une exception si le résultat de la méthode n'est pas du JSON
    public function testRender2(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $view = new class extends JSONView {
            protected function post(Request $request): array {
                return  ['message' => NAN];
            }
        };

        $this->expectException(\TypeError::class);

        $view->render(new Request());
    }
}