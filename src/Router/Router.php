<?php declare(strict_types=1);

namespace Framework312\Router;

interface Router {
    public function register(string $path, string|object $class_or_view);
    public function serve(mixed ...$args);
}

?>
