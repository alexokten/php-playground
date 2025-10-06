<?php

declare(strict_types=1);

namespace Router;

use Exception;
use Spatie\Regex\Regex;

class RouterUtils
{
    public const string PARAMETER_MATCH_PATTERN = '/\:\w+/';

    public static function matchParamPattern(string $routeSegment): bool
    {
        return Regex::match(self::PARAMETER_MATCH_PATTERN, $routeSegment)->hasMatch();
    }
}
class RouteItem
{
    public string $method;
    public string $slug;
    public array $callback;
    public ?string $controllerClass = null;
    public ?string $controllerMethod = null;

    public function __construct(
        string $method,
        string $slug,
        callable | array $callback
    ) {
        $this->method = $method;
        $this->slug = $slug;
        $this->callback = $callback;

        if (is_array($callback)) {
            $this->controllerClass = $callback[0];
            $this->controllerMethod = $callback[1];
        }
    }

    public static function get(string $slug, array | callable $callback): self
    {
        return new self('GET', $slug, $callback);
    }

    public static function post(string $slug, array | callable $callback): self
    {
        return new self('POST', $slug, $callback);
    }

    public static function put(string $slug, array | callable $callback): self
    {
        return new self('PUT', $slug, $callback);
    }

    public static function delete(string $slug, array | callable $callback): self
    {
        return new self('DELETE', $slug, $callback);
    }

    public function isControllerFunction(): bool
    {
        return $this->controllerClass !== null;
    }
}

class RequestItem
{
    public function __construct(
        public string $method,
        public string $url,
        public array $headers = [],
        public string $body = '',
        public array $params = []
    ) {}
}

class ResponseItem
{
    public static function sendResponse(
        string $responseJson
    ): void {
        ray($responseJson);
        echo $responseJson;
    }
}

class Router
{
    private array $registeredRoutes = [];

    public function dispatch(): void
    {
        $request = RequestFactory::createFromGlobals();
        $route = $this->findMatchingRoute($request->url, $request->method);

        $request->params = $this->extractUrlParameters($route->slug, $request->url);

        if ($route->isControllerFunction()) {
            $controllerClass = $route->controllerClass;
            assert($controllerClass !== null && class_exists($controllerClass));
            $controller = new $controllerClass();
            $controllerMethod = $route->controllerMethod;
            assert($controllerMethod !== null);
            $controller->$controllerMethod($request);
        } else {
            ($route->callback)($request);
        }
    }

    private function registerRoute(RouteItem $routeDefinition): void
    {
        array_push($this->registeredRoutes, $routeDefinition);
    }

    public function get(string $urlPattern, array | callable $callback): Router
    {
        $routeItem = RouteItem::get(slug: $urlPattern, callback: $callback);
        $this->registerRoute($routeItem);
        return $this;
    }

    public function post(string $urlPattern, array | callable $callback): Router
    {
        $routeItem = RouteItem::post(slug: $urlPattern, callback: $callback);
        $this->registerRoute($routeItem);
        return $this;
    }

    public function put(string $urlPattern, array | callable $callback): Router
    {
        $routeItem = RouteItem::get(slug: $urlPattern, callback: $callback);
        $this->registerRoute($routeItem);
        return $this;
    }

    public function delete(string $urlPattern, array | callable $callback): Router
    {
        $routeItem = RouteItem::get(slug: $urlPattern, callback: $callback);
        $this->registerRoute($routeItem);
        return $this;
    }

    private function findMatchingRoute(string $incomingUrlPath, string $method): RouteItem
    {
        $utils = new RouterUtils();

        $this->verifyHttpMethod($method);

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
        $request = RequestFactory::createFromGlobals();
        $actualHttpMethod = $request->method;

        if ($expectedHttpMethod !== $actualHttpMethod) {
            throw new Exception('Request method does not match');
        }

        return true;
    }

    private function extractUrlParameters(string $routePattern, string $actualUrl): array
    {
        $extractedParameters = [];

        $routeSegments = explode('/', $routePattern);
        $urlSegments = explode('/', $actualUrl);

        for ($segmentIndex = 0; $segmentIndex < count($routeSegments); $segmentIndex++) {
            $routeSegment = $routeSegments[$segmentIndex];
            $urlSegment = $urlSegments[$segmentIndex];

            if (RouterUtils::matchParamPattern($routeSegment)) {
                $extractedParameters += [$routeSegment => $urlSegment];
            }

            if ($routeSegment !== $urlSegment) {
                continue;
            }
        }
        return $extractedParameters;
    }
}

class RequestFactory
{
    public static function createFromGlobals(): RequestItem
    {
        $body = file_get_contents('php://input');
        return new RequestItem(
            method: $_SERVER['REQUEST_METHOD'] ?? 'GET',
            url: $_SERVER['REQUEST_URI'] ?? '/',
            headers: getallheaders() ?: [],
            body: $body !== false ? $body : '',
        );
    }
}
