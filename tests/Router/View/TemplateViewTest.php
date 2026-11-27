<?php declare(strict_types=1);

namespace Framework312\Tests\Router\View;

use Framework312\Router\Request;
use Framework312\Router\View\TemplateView;
use Framework312\Router\Exception\HttpMethodNotImplemented;
use PHPUnit\Framework\TestCase;
use Framework312\Template\Renderer;

final class TemplateViewTest extends TestCase
{

    // Vérifie que le constructeur enregistre la view auprès du renderer
    public function testConstructeur1(): void
    {
        $renderer = $this->createMock(Renderer::class);

        // On vérifie que register() est appelé une fois, avec une string (le nom de la classe)
        $renderer->expects($this->once())
                 ->method('register')
                 ->with($this->isType('string'));

        new class($renderer) extends TemplateView {};
    }
    // Test la réaction du controleur si on lui passe autre chose qu'un Renderer
    public function testConstructeur2(): void
    {
        $this->expectException(\TypeError::class);

        new class('pas un renderer') extends TemplateView {};
    }


    public function testUseTemplateRenvoieTrue(): void
    {
        $this->assertTrue(TemplateView::use_template());
    }

    
    // Teste que render() appelle la méthode correspondant au verbe HTTP, passe les données au renderer et renvoie le HTML
    public function testRender1(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // On crée un mock du renderer qui va faire semblant de rendre le template et renvoyer du HTML
        $renderer = $this->createMock(Renderer::class);
        $renderer->expects($this->once())
                 ->method('render')
                 ->with(['titre' => 'Dune'], 'show.twig')
                 ->willReturn('<h1>Dune</h1>');

        $view = new class($renderer) extends TemplateView {
            protected function get(Request $request): array
            {
                return ['template' => 'show.twig', 'data' => ['titre' => 'Dune']];
            }
        };

        $response = $view->render(new Request());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('<h1>Dune</h1>', $response->getContent());
        $this->assertStringContainsString('text/html', $response->headers->get('content-type'));
    }
    // Teste le comportement de render() quand le résultat de la requête n'est pas un tableau
    public function testRender2(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $view = new class($this->createMock(Renderer::class)) extends TemplateView {
            protected function get(Request $request): string
            {
                return '<h1>Dune</h1>';
            }
        };

        $this->expectException(\TypeError::class);

        $view->render(new Request());
    }
    // Teste le comportement de render() quand le tableau retourné par la méthode ne contient pas une des clés 'template' et 'data'
    public function testRender3(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $view = new class($this->createMock(Renderer::class)) extends TemplateView {
            protected function get(Request $request): array
            {
                return ['template' => 'show.twig'];   // il manque 'data'
            }
        };

        $this->expectException(\InvalidArgumentException::class);

        $view->render(new Request());
    }

    // Teste que render() refuse un verbe non prévu (ex : HEAD) au lieu d'appeler une méthode inexistante
    public function testRenderVerbeNonAutorise(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'HEAD';

        $view = new class($this->createMock(Renderer::class)) extends TemplateView {};

        $this->expectException(HttpMethodNotImplemented::class);
        $this->expectExceptionCode(405);

        $view->render(new Request());
    }
}
