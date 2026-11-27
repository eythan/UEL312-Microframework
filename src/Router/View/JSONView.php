<?php

namespace Framework312\Router\View;


use Framework312\Router\Request;
use Symfony\Component\HttpFoundation\Response;
use TypeError;

abstract class JSONView extends BaseView {
    public static function use_template(): bool {
        return false;
    }
    public function render(Request $request): Response {
        $methodHTTP = $request->getMethod();
        // Conversion en minuscules pour correspondre au méthode dans BaseView.php
        $methodHTTPClean = strtolower($methodHTTP);
        $content = $this->$methodHTTPClean($request);
        $JSONFormat = json_encode($content);

        // On vérifie si JSONFormat est un string, comme Reponse demande un retour en string
        if(!is_string($JSONFormat)) {
            throw new TypeError("Le contenu JSON doit être de type string");
        } else {
            $response = new Response($JSONFormat, 200, ['content-type' => 'application/json']);
            return $response;
        }
    }
}

?>