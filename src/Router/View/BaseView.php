<?php declare(strict_types=1);

namespace Framework312\Router\View;

use Framework312\Router\Exception as RouterException;
use Framework312\Router\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseView {
    protected function get(Request $request): mixed {
        throw new RouterException\HttpMethodNotImplemented(static::class, 'GET');
    }

    protected function post(Request $request): mixed {
        throw new RouterException\HttpMethodNotImplemented(static::class, 'POST');
    }

    protected function patch(Request $request): mixed {
        throw new RouterException\HttpMethodNotImplemented(static::class, 'PATCH');
    }

    protected function put(Request $request): mixed {
        throw new RouterException\HttpMethodNotImplemented(static::class, 'PUT');
    }

    protected function delete(Request $request): mixed {
        throw new RouterException\HttpMethodNotImplemented(static::class, 'DELETE');
    }

    abstract static public function use_template(): bool;
    abstract public function render(Request $request): Response;
}

?>
