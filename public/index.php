<?php

declare(strict_types=1);


require_once __DIR__ . '/../vendor/autoload.php';

use Spatie\Regex\Regex;

ray()->clearAll();

class Router
{
    private $routesList = [];

    public function dispatch(): void
    {
        $requestURL = $_SERVER['REQUEST_URI'];

        $req = ['params' => []];
        $res = [];
        $req['params'] = '123';

        $route = $this->matchRoute($requestURL);
        $this->verifyMethod($route['method']);
        // $req['params'] = $this->extractParams($requestURL);
        //
        $this->extractParams($route['slug']);
        $route['controller']($req, $res);
    }

    public function get(string $slug, callable $controller): void
    {
        $routeArray = [
            'slug' => $slug,
            'controller' => $controller,
            'method' => 'GET'
        ];

        $this->addRoute($routeArray);
    }

    public function post(string $slug, callable $controller): void
    {
        $routeArray = [
            'slug' => $slug,
            'controller' => $controller,
            'method' => 'POST'
        ];

        $this->addRoute($routeArray);
    }

    public function put(string $slug, callable $controller): void
    {
        $routeArray = [
            'slug' => $slug,
            'controller' => $controller,
            'method' => 'PUT'
        ];

        $this->addRoute($routeArray);
    }

    public function delete(string $slug, callable $controller): void
    {
        $routeArray = [
            'slug' => $slug,
            'controller' => $controller,
            'method' => 'DELETE'
        ];

        $this->addRoute($routeArray);
    }

    private function addRoute(array $route): void
    {
        array_push($this->routesList, $route);
        ray($this->routesList, 'Added');
    }

    private function matchRoute(string $requestSlug): array
    {
        foreach ($this->routesList as $route) {
            if ($route['slug'] === $requestSlug) {
                return $route;
            }
        }
        throw new Exception('Route not matched');
    }

    private function verifyMethod(string $requestMethod): bool
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($requestMethod !== $requestMethod) {
            throw new Exception('Request method does not match');
        }
        return true;
    }

    private function extractParams(string $routeSlug): string
    {
        if (Regex::match('/\{\w+\}|:\w+/', $routeSlug)->hasMatch()) {
            $params = [];
            $matches = Regex::matchAll('/\{\w+\}|:\w+/', $routeSlug);

            ray($matches);
            foreach ($matches->results() as $match) {
                ray($match);
            }


            return Regex::match('/\{\w+\}|:\w+/', $routeSlug)->result();
        }
        return '';
    }
}



$router = new Router();

$router->get('/api/user/:id/:test', function ($req, $res) {
    ray($req['params'], ': PARAMS');
});

$router->dispatch();

require __DIR__ . '/../controllers/home.php';
