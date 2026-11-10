<?php declare(strict_types=1);

namespace Framework312\Router\Exception;

use Symfony\Component\HttpFoundation\Response;

class InvalidViewImplementation extends Exception {
    public function __construct(string $classname) {
        parent::__construct("Class {$classname} does not extend Framework312\Router\View\BaseView class, cannot be used as a Router's view.", Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

?>
