<?php

declare(strict_types=1);

use Spatie\Regex\Regex;
use App\Helpers\Response;

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

    public static function get(string $slug, array | callable $callback)
    {
        return new self('GET', $slug, $callback);
    }

    public static function post(string $slug, array | callable $callback)
    {
        return new self('POST', $slug, $callback);
    }

    public static function put(string $slug, array | callable $callback)
    {
        return new self('PUT', $slug, $callback);
    }

    public static function delete(string $slug, array | callable $callback)
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
    ) {
        ray($responseJson);
        echo $responseJson;
    }
}

class Router
{
    private $registeredRoutes = [];

    public function dispatch(): void
    {
        $request = RequestFactory::createFromGlobals();
        $route = $this->findMatchingRoute($request->url, $request->method);

        $request->params = $this->extractUrlParameters($route->slug, $request->url);

        $controller = new $route->controllerClass();
        $controller->{$route->controllerMethod}($request);
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

    public function post(string $urlPattern, array | callable $callback): void
    {
        $routeItem = RouteItem::get(slug: $urlPattern, callback: $callback);
        $this->registerRoute($routeItem);
    }

    public function put(string $urlPattern, array | callable $callback): void
    {
        $routeItem = RouteItem::get(slug: $urlPattern, callback: $callback);
        $this->registerRoute($routeItem);
    }

    public function delete(string $urlPattern, array | callable $callback): void
    {
        $routeItem = RouteItem::get(slug: $urlPattern, callback: $callback);
        $this->registerRoute($routeItem);
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

class RequestFactory
{
    public static function createFromGlobals(): RequestItem
    {
        return new RequestItem(
            method: $_SERVER['REQUEST_METHOD'],
            url: $_SERVER['REQUEST_URI'],
            headers: getallheaders(),
            body: file_get_contents('php://input')
        );
    }
}
