<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Spatie\Regex\Regex;

ray()->clearAll();

class Router
{
    private $routesList = [];
    private $matchBracesAndSemiColons = '/\{\w+\}|:\w+/';

    public function dispatch(): void
    {
        $requestURL = $_SERVER['REQUEST_URI'];

        $req = ['params' => []];
        $res = ['send' => function () {
            echo "Route accessed";
        }];

        $route = $this->matchRoute($requestURL);
        $this->verifyMethod($route['method']);
        $extractedParamKeys = $this->extractParams($route['slug'], $requestURL);
        $req = ['params' => $extractedParamKeys];
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

            $splitRouteSlug = explode('/', $route['slug']);
            $splitRequestSlug = explode('/', $requestSlug);

            if (count($splitRouteSlug) !== count($splitRequestSlug)) {
                continue;
            }

            $isMatch = true;

            for ($i = 0; $i < count($splitRouteSlug); $i++) {

                $routeSegment = $splitRouteSlug[$i];
                $requestSegment = $splitRequestSlug[$i];

                if (Regex::match($this->matchBracesAndSemiColons, $routeSegment)->hasMatch()) {
                    continue;
                }

                if ($routeSegment !== $requestSegment) {
                    $isMatch = false;
                    break;
                }
            }
            if ($isMatch) {
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

    private function extractParams(string $routeSlugToMatch, string $requestSlugToMatch): array
    {
        $extractedParams = [];

        $splitRouteSlug = explode('/', $routeSlugToMatch);
        $splitRequestSlug = explode('/', $requestSlugToMatch);

        for ($i = 0; $i < count($splitRouteSlug); $i++) {
            $routeSegment = $splitRouteSlug[$i];
            $requestSegment = $splitRequestSlug[$i];

            if (Regex::match($this->matchBracesAndSemiColons, $routeSegment)->hasMatch()) {
                $extractedParams = [...$extractedParams, $requestSegment];
            }

            if ($routeSegment !== $requestSegment) {
                continue;
            }
        }
        return $extractedParams;
    }
}



$router = new Router();

$router->get('/api/user/:id/:location', function ($req, $res) {
    $res['send']();
});

$router->dispatch();

require __DIR__ . '/../controllers/home.php';
