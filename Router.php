<?php

declare(strict_types=1);

use Spatie\Regex\Regex;

class RouterUtils
{
    public const PARAMETER_MATCH_PATTERN = '/\:\w+/';

    public static function matchParamPattern(string $routeSegment)
    {
        return Regex::match(self::PARAMETER_MATCH_PATTERN, $routeSegment)->hasMatch();
    }
}

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
    private $registeredRoutes = [];

    public function dispatch(): void
    {
        $incomingUrl = $_SERVER['REQUEST_URI'];

        $matchedRoute = $this->findMatchingRoute($incomingUrl);

        $this->verifyHttpMethod($matchedRoute->method);

        $urlParameters = $this->extractUrlParameters($matchedRoute->slug, $incomingUrl);

        $request = new RequestItem($urlParameters);
        $response = new ResponseItem();

        $routeHandler = $matchedRoute->callback;
        $routeHandler($request, $response);
    }

    public function get(string $urlPattern, callable $routeHandler): void
    {
        $routeItem = new RouteItem(
            method: 'GET',
            slug: $urlPattern,
            callback: $routeHandler
        );
        $this->registerRoute($routeItem);
    }

    public function post(string $urlPattern, callable $routeHandler): void
    {
        $routeItem = new RouteItem(
            method: 'POST',
            slug: $urlPattern,
            callback: $routeHandler
        );
        $this->registerRoute($routeItem);
    }

    public function put(string $urlPattern, callable $routeHandler): void
    {
        $routeItem = new RouteItem(
            method: 'PUT',
            slug: $urlPattern,
            callback: $routeHandler
        );
        $this->registerRoute($routeItem);
    }

    public function delete(string $urlPattern, callable $routeHandler): void
    {
        $routeItem = new RouteItem(
            method: 'DELETE',
            slug: $urlPattern,
            callback: $routeHandler
        );
        $this->registerRoute($routeItem);
    }

    private function registerRoute(RouteItem $routeDefinition): void
    {
        array_push($this->registeredRoutes, $routeDefinition);
    }

    private function findMatchingRoute(string $incomingUrlPath): RouteItem
    {
        $utils = new RouterUtils();

        foreach ($this->registeredRoutes as $candidateRoute) {
            if ($candidateRoute->slug === $incomingUrlPath) {
                return $candidateRoute;
            }

            $routeSegments = explode('/', $candidateRoute->slug);
            $urlSegments = explode('/', $incomingUrlPath);

            if (count($routeSegments) !== count($urlSegments)) {
                continue;
            }

            $isMatch = true;

            for ($segmentIndex = 0; $segmentIndex < count($routeSegments); $segmentIndex++) {

                $routeSegment = $routeSegments[$segmentIndex];
                $urlSegment = $urlSegments[$segmentIndex];

                if ($utils->matchParamPattern($routeSegment)) {
                    continue;
                }

                if ($routeSegment !== $urlSegment) {
                    $isMatch = false;
                    break;
                }
            }
            if ($isMatch) {
                return $candidateRoute;
            }
        }
        throw new Exception('Route not matched');
    }

    private function verifyHttpMethod(string $expectedHttpMethod): bool
    {
        $actualHttpMethod = $_SERVER['REQUEST_METHOD'];
        if ($expectedHttpMethod !== $actualHttpMethod) {
            throw new Exception('Request method does not match');
        }
        return true;
    }

    private function extractUrlParameters(string $routePattern, string $actualUrl): array
    {
        $utils = new RouterUtils();

        $extractedParameters = [];

        $routeSegments = explode('/', $routePattern);
        $urlSegments = explode('/', $actualUrl);

        for ($segmentIndex = 0; $segmentIndex < count($routeSegments); $segmentIndex++) {
            $routeSegment = $routeSegments[$segmentIndex];
            $urlSegment = $urlSegments[$segmentIndex];

            if ($utils->matchParamPattern($routeSegment)) {
                $extractedParameters += [$routeSegment => $urlSegment];
            }

            if ($routeSegment !== $urlSegment) {
                continue;
            }
        }
        return $extractedParameters;
    }
}
