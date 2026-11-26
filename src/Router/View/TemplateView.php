<?php 

namespace Framework312\Router\View;

use Framework312\Template\Renderer;
use Framework312\Router\Request;
use Symfony\Component\HttpFoundation\Response;
use TypeError;
use InvalidArgumentException;

abstract class TemplateView extends BaseView {
    private Renderer $renderer;
    public function __construct(Renderer $renderer) {
        $this->renderer = $renderer;
        $renderer->register(static::class);
    }
    public static function use_template(): bool {
        return true;
    }
    public function render(Request $request): Response {
        $methodHTTP = $request->getMethod();
        // Conversion en minuscules pour correspondre au méthode dans BaseView.php
        $methodHTTPClean = strtolower($methodHTTP);
        $result = $this->$methodHTTPClean($request);

        // Vérification du type comme les méthodes dans BaseView.php renvoie "mixed"
        if(!is_array($result)) {
            throw new TypeError("le résultat de la méthode doit être de type tableau");
        } else {
            if(!array_key_exists('template', $result) || !array_key_exists('data', $result)) {
                throw new InvalidArgumentException;
            } else {
                $rendererOutput = $this->renderer->render($result['data'], $result['template']);
                $response = new Response($rendererOutput, 200, ['content-type' => 'text/html']);
                return $response;
            }
        }
    }
}

?>