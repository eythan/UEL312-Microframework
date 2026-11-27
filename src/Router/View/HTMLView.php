<?php

namespace Framework312\Router\View;

use Framework312\Router\Exception\HttpMethodNotImplemented;
use Framework312\Router\Request;
use Symfony\Component\HttpFoundation\Response;
use TypeError;

abstract class HTMLView extends BaseView {
    static public function use_template(): bool {
        return false;
    }
    public function render(Request $request): Response {
        $methodHTTP = $request->getMethod();
        // Conversion en minuscules pour correspondre au méthode dans BaseView.php
        $methodHTTPClean = strtolower($methodHTTP);

        // Seuls les verbes prévus par BaseView sont autorisés : sinon la requête
        // pourrait appeler n'importe quelle méthode (head(), render()...)
        if (!in_array($methodHTTPClean, self::VERBES_AUTORISES, true)) {
            throw new HttpMethodNotImplemented(static::class, $methodHTTP);
        }
        $content = $this->$methodHTTPClean($request);

        // Vérification du type comme les méthodes dans BaseView.php renvoie "mixed"
        if(is_string($content)) {
            $response = new Response($content, 200, ['content-type' => 'text/html']);
            return $response;
        } else {
            throw new TypeError("Le content doit être de type string");
        }

    }
}

?>