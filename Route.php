<?php

    class Route {

        private $path;
        private $callable;
        private $matches = [];
        private $params = [];

        public function __construct($path, $callable) {
            $this->path = trim($path, '/');
            $this->callable = $callable;
        }

        public function match($url)
        {
            $url = trim($url, '/');
            $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
            $regex = "#^$path$#i";
            if(!preg_match($regex, $url, $matches))
            {
                return false;
            }
            array_shift($matches);
            $this->matches = $matches;
            return true;
        }

        public function call() 
        {
            return call_user_func_array($this->callable, $this->matches);
        }

        public function with($param, $regex)
        {
            $this->params[$param] = str_replace('(', '(?:', $regex);
            return $this;
        }

        public function paramMatch($match)
        {
            if(isset($this->params[$match[1]]))
            {
                return '('.$this->params[$match[1]]. ')';
            }
            return '([^/]+)';
        }
    }