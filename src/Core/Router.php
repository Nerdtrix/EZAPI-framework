<?php
  namespace Core;
<<<<<<< HEAD
  use \Exception;   
  use Core\Request;
  use Core\Constant;
  use Core\DI;
  use Core\Languages\Translator;
  
  

  class Router 
  {
    public
        $request, 
        $lang,
        $di;

    public function __construct() 
    {
      #Inject Dependencies
      $dependencyInjection = new DI();
      $this->di = $dependencyInjection->load(get_called_class());

      #instantiate response request
      $this->request = new Request();

      #Add headers to the request method
      $this->request->headers();

      $this->lang = new Translator();
=======
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
>>>>>>> rebuildtest
    }

    public function __destruct()
    {
<<<<<<< HEAD
      $this->di = null;
=======
      unset($this->request);
>>>>>>> rebuildtest
    }
  }