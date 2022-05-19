<?php
  namespace Core; 

  
  class Router 
  {
    public IRequest $request;

    public function __construct() 
    {
      //Get from singleton later
      $this->request = new Request();

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