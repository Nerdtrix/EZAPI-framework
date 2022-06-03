<?php
    namespace Controllers;
    use Core\Router;
    use Services\{IAuthenticationService};
    use Core\Exceptions\ApiError;

    class Authentication extends Router
    {
        protected IAuthenticationService $m_authService;


        public function __construct(IAuthenticationService $authService)
        {
            $this->m_authService = $authService;

            parent::__construct();
        }     


        /**
         * @api route /
         * @method get
         * @return object
         */
        public function index() : void
        {  
            $this->request->response((object)[
                "greeting" => "Hello world",
                "app_name" => EZENV["APP_NAME"],
                "version" => EZENV["APP_VERSION"]
            ]);
        }
      
        
        /**
         * @api route /authentication/Login
         * @method post
         * @param object $input {usernameOrEmail: required string, password: required string, rememeberMe: obtional bool}
         * @return object
         * @throws ApiError
         */
        public function Login(object $input) : void
        { 
            if(is_null($input))
            {
                throw new ApiError("Invalid request body");
            }
            
            if(empty($input->usernameOrEmail))
            {
                throw new ApiError("Username_or_email_is_required");
            }

            if(empty($input->password))
            {
                throw new ApiError("password_is_required");
            }

            $response = $this->m_authService->authenticate(
                usernameOrEmail: $input->usernameOrEmail,
                password: $input->password,
                rememberMe: $input->rememberMe ?? false
            );

            $this->request->response($response);
        }


        public function otpRequest(object $input) : void
        {
            
        }

        public function logout() : void {}

        public function extend() : void {}
    }
?>