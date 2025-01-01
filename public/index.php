<?php
  require '../helpers.php';
  require basePath('Framework/Database.php');
  require basePath('Framework/Router.php');

  // Instatiate the router
  $router = new Router();
  // Get Routes
  $routes = require basePath('routes.php');
  // Get current uri and HTTP Methpd
  $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $method = $_SERVER['REQUEST_METHOD'];
  // Route the request
  $router->route($uri, $method);
?>
