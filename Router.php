<?php

//use Route;

  class Router {

    private $url;
    private $routes = [];

    public function __construct($url) 
    {
      $this->url = $url;
    }

    public function get($path, $callable)
    {
      $route = new Route($path, $callable);
      $this->routes['GET'][] = $route;
      $route;
    }

    public function run() 
    {
      if(!isset($this->routes[$_SERVER['REQUEST_METHOD']]))
      {
        throw new RouterException('REQUEST_METHOD doesn\'t exist');
      }
      foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $route)
      {
        if($route->match($this->url))
        {
          return $route->call();
        }
      }
      throw new RouterException('No matching routes');
    }

  }