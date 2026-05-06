<?php

declare(strict_types=1);

namespace App\Core;

use Closure;

class Router
{
    /** @var array<int, array{pattern: string, handler: Closure}> */
    private array $routes = [];

    private ?Closure $notFoundHandler = null;

    public function get(string $pattern, Closure $handler): void
    {
        $this->routes[] = ['pattern' => $pattern, 'handler' => $handler];
    }

    public function notFound(Closure $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    public function dispatch(string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        foreach ($this->routes as $route) {
            $regex = '#^' . $route['pattern'] . '$#';
            if (preg_match($regex, $path, $matches)) {
                array_shift($matches);
                ($route['handler'])(...$matches);
                return;
            }
        }

        if ($this->notFoundHandler !== null) {
            ($this->notFoundHandler)();
            return;
        }

        http_response_code(404);
        echo '404 Not Found';
    }
}
