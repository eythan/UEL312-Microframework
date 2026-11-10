<?php declare(strict_types=1);

namespace Framework312\Template;

interface Renderer {
    public function render(mixed $data, string $template): string;
    public function register(string $tag);
}

?>
