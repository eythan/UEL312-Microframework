<?php declare(strict_types=1);

namespace Framework312\Router\Exception;

use Symfony\Component\HttpFoundation\Response;

class HttpMethodNotImplemented extends Exception {
    public function __construct(string $classname, string $method) {
        parent::__construct("Method {$method} not implemented for class {$classname}", Response::HTTP_METHOD_NOT_ALLOWED);
    }
}

?>
