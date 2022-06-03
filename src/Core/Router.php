<?php
  namespace Core;
  class Router 
  {
    public IRequest $request;


    // public function __construct(?IRequest $request = null, ?ITranslator $language = null) 
    // {
    //   //Get from singleton later
    //   $this->request = $request;

    //   $this->lang = $language;
    // }

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