<?php
    namespace Routes;
    use Core\Router;
    use Core\Constant;
    
    use Core\Exceptions\ApiError;
  

    class User extends Router
    {
        public function __construct()
        {
            parent::__construct();
        }


        /**
         * 
         */
        public function index() : void
        {
            $this->request->response(200, [
                "greeting" => $this->lang->translate("hello_world"),
                "appVersion" => EZENV["APP_VERSION"],
                "poweredBy" => EZENV["APP_NAME"]
            ]);
        }


        /**
         * 
         */
        public function locale() : void
        {
            $this->request->response(200, [
                "defaultLocale" =>  EZENV["DEFAULT_LOCALE"],
                "currentLocale" => $this->lang->currentLocale(),
                "supportedLocales" => $this->lang->list()
            ]);
        }


        /**
         * 
         */
        public function setLocale() : void
        {
            #Receive params and sanitize them.
           $input = $this->request->inputJson(true);


            #Change language locale
            if(!$this->lang->setLocale($input->locale))
            {
                throw new apiError (Constant::UNABLE_TO_SET_LOCALE);
            }
            
            #Success response
            $this->request->response(200, [Constant::MESSAGE => Constant::SUCCESS]);
        }


        #in progress
        public function login() : void {}
        public function logout() : void {}
        public function info() : void {}
        public function register() : void {}
        public function resetPsw() : void {}
        public function update() : void {}
        public function extend() : void {}
        public function activate() : void {}


    }