<?php
  namespace Core;
  use Core\{IRequest, Request};

  class Router 
  {
    public IRequest $request;

    public function __construct()
    {
      $this->request = new Request;
    }

    public function request()
    {
      return $this->request;
    }

    public function __destruct()
    {
      unset($this->request);
    }
  }
?>