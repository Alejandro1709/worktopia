<?php

namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorize;

class Router {
  protected $routes = [];

  /**
   * Add a new route
   *
   * @param string $method
   * @param string $uri
   * @param string $action
   * @param array $middleware
   * @return void
   */

  public function registerRoute($method, $uri, $action, $middleware = []) {
    list($controller, $controllerMethod) = explode('@', $action);

    $this->routes[] = [
      'method' => $method,
      'uri' => $uri,
      'controller' => $controller,
      'controllerMethod' => $controllerMethod,
      'middleware' => $middleware
    ];
  }

  /**
   * Add a GET Route
   * 
   * @param string $uri
   * @param string $controller
   * @param array $middleware
   * @return void
   */

   public function get($uri, $controller, $middleware = []) {
    $this->registerRoute('GET', $uri, $controller, $middleware);
   } 

  /**
   * Add a POST Route
   * 
   * @param string $uri
   * @param string $controller
   * @param array middleware
   * @return void
   */

   public function post($uri, $controller, $middleware = []) {
    $this->registerRoute('POST', $uri, $controller, $middleware);
   }

  /**
   * Add a PUT Route
   * 
   * @param string $uri
   * @param string $controller
   * @param array middleware
   * @return void
   */

   public function put($uri, $controller, $middleware = []) {
    $this->registerRoute('PUT', $uri, $controller, $middleware);
   }

  /**
   * Add a DELETE Route
   * 
   * @param string $uri
   * @param string $controller
   * @param array middleware
   * @return void
   */

   public function delete($uri, $controller, $middleware = []) {
    $this->registerRoute('DELETE', $uri, $controller, $middleware);
   }

   /**
    * Route the request
    *
    * @param string $uri
    * @return void
    */

    public function route($uri) {
      $requestMethod = $_SERVER['REQUEST_METHOD'];

      // Check for _method input
      if ($requestMethod === 'POST' && isset($_POST['_method'])) {
        // Override the request method with the value of _method
        $requestMethod = strtoupper($_POST['_method']);
      }

      foreach ($this->routes as $route) {
        // Split the current URI into segments
        $uriSegments = explode('/', trim($uri, '/'));
        // Split the route URI into segments
        $routeSegment = explode('/', trim($route['uri'], '/'));

        $match = true;

        // Check if the number of segments matches
        if (count($uriSegments) === count($routeSegment) && strtoupper($route['method'] === $requestMethod)) {
          $params = [];

          $match = true;

          for ($i = 0; $i < count($uriSegments); $i++) {
            // If the uri's do not match and there is no params
            if ($routeSegment[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegment[$i])) {
              $match = false;
              break;
            }

            // Check for the param and add to $params array
            if (preg_match('/\{(.+?)\}/', $routeSegment[$i], $matches)) {
              $params[$matches[1]] = $uriSegments[$i];
            }
          }

          if ($match) {

            foreach ($route['middleware'] as $middleware) {
              (new Authorize())->handle($middleware);
            }

            $controller = 'App\\Controllers\\' . $route['controller'];
            $controllerMethod = $route['controllerMethod'];
            
            // Instatiate the controller and call the method
            $controllerInstance = new $controller();
            $controllerInstance->$controllerMethod($params);
            return;
          }
        }
      }

      ErrorController::notFound();
    }
}
?>