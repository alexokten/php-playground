<?php

declare(strict_types=1);

use Spatie\Regex\Regex;

class RouteItem
{
    public string $method;
    public string $slug;
    public $callback;

    public function __construct(
        string $method,
        string $slug,
        callable $callback
    ) {
        $this->method = $method;
        $this->slug = $slug;
        $this->callback = $callback;
    }
}

class RequestItem
{
    public array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }
}

class ResponseItem
{
    public static function sendResponse(string $responseMessage = "Route found")
    {
        echo $responseMessage;
    }
}

class Router
{
    private $routesList = [];
    private $matchBracesAndSemiColonsRegex = '/\:\w+/';

    public function dispatch(): void
    {
        $requestURL = $_SERVER['REQUEST_URI'];

        $route = $this->matchRoute($requestURL);

        $this->verifyMethod($route->method);

        $extractedParams = $this->extractParams($route->slug, $requestURL);

        $req = new RequestItem($extractedParams);
        $res = new ResponseItem();

        $callback = $route->callback;
        $callback($req, $res);
    }

    public function get(string $slug, callable $callback): void
    {
        $routeItem = new RouteItem(
            method: 'GET',
            slug: $slug,
            callback: $callback
        );
        $this->addRoute($routeItem);
    }

    public function post(string $slug, callable $callback): void
    {
        $routeItem = new RouteItem(
            method: 'POST',
            slug: $slug,
            callback: $callback
        );
        $this->addRoute($routeItem);
    }

    public function put(string $slug, callable $callback): void
    {
        $routeItem = new RouteItem(
            method: 'PUT',
            slug: $slug,
            callback: $callback
        );
        $this->addRoute($routeItem);
    }

    public function delete(string $slug, callable $callback): void
    {
        $routeItem = new RouteItem(
            method: 'DELETE',
            slug: $slug,
            callback: $callback
        );
        $this->addRoute($routeItem);
    }

    private function addRoute(RouteItem $routeItem): void
    {
        array_push($this->routesList, $routeItem);
    }

    private function matchRoute(string $requestSlug): RouteItem
    {
        foreach ($this->routesList as $route) {
            if ($route->slug === $requestSlug) {
                return $route;
            }

            $splitRouteSlug = explode('/', $route->slug);
            $splitRequestSlug = explode('/', $requestSlug);

            if (count($splitRouteSlug) !== count($splitRequestSlug)) {
                continue;
            }

            $isMatch = true;

            for ($i = 0; $i < count($splitRouteSlug); $i++) {

                $routeSegment = $splitRouteSlug[$i];
                $requestSegment = $splitRequestSlug[$i];

                if (Regex::match($this->matchBracesAndSemiColonsRegex,  $routeSegment)->hasMatch()) {
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

    private function verifyMethod(string $routeRequestMethod): bool
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($routeRequestMethod !== $requestMethod) {
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
            $matchResult =  Regex::match($this->matchBracesAndSemiColonsRegex,  $routeSegment);
            if (Regex::match($this->matchBracesAndSemiColonsRegex,  $routeSegment)->hasMatch()) {
                $extractedParams += [$routeSegment => $requestSegment];
            }

            if ($routeSegment !== $requestSegment) {
                continue;
            }
        }
        return $extractedParams;
    }
}
