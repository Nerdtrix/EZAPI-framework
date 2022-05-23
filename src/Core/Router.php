<?php
  namespace Core;
  use Core\Language\ITranslator;

  class Router 
  {
    public IRequest $request;

    public ITranslator $lang;

    public function __construct(?IRequest $request = null, ?ITranslator $language = null) 
    {
      //Get from singleton later
      $this->request = $request;

      $this->lang = $language;
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