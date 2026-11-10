<?php declare(strict_types=1);

namespace Framework312\Router;

use Symfony\Component\HttpFoundation\{Request as SymfonyRequest, InputBag};

class Request extends SymfonyRequest {
    private array $context = [];

    public function __construct(mixed ...$args) {
        parent::__construct($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);

        $content_type = $this->headers->get('CONTENT_TYPE', '');
        $method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));

        $is_urlencoded = str_starts_with($content_type, 'application/x-www-form-urlencoded');
        $modification_methods = ['PUT', 'DELETE', 'PATCH'];
        if ($is_urlencoded && \in_array($method, $modification_methods, true)) {
            parent::parse_str($this->getContent(), $data);
            $this->request = new InputBag($data);
        }

        if (func_num_args() > 0) {
            $args = array_filter($args, 'is_string');
        }
        $this->context = array_map('strtolower', $args);
    }

    public function fromContext(string $key): mixed {
        return $this->context[strtolower($key)] ?? null;
    }
}

?>
