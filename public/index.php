<?php
  require '../helpers.php';
  require basePath('Database.php');
  require basePath('Router.php');

  // Instatiate the router
  $router = new Router();
  // Get Routes
  $routes = require basePath('routes.php');
  // Get current uri and HTTP Methpd
  $uri = $_SERVER['REQUEST_URI'];
  $method = $_SERVER['REQUEST_METHOD'];
  // Route the request
  $router->route($uri, $method);
?>
