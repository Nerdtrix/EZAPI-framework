<?php
    namespace Controllers;
    use Core\Router;
    use Services\{IAuthService};
    use Core\Exceptions\ApiError;


    class User extends Router
    {
        protected IAuthService $m_authService;

        public function __construct(IAuthService $authService)
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
         * @api route /user/auth
         * @method post
         * @param object $input {usernameOrEmail: string, password: string, rememeberMe: bool}
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
                throw new ApiError("Username or email is required");
            }

            if(empty($input->password))
            {
                throw new ApiError("password is required");
            }
           
            $response = $this->m_authService->Authenticate(
                usernameOrEmail: $input->usernameOrEmail,
                password: $input->password,
                rememberMe: (bool)$input->rememberMe ?? false
            );

            $this->request->response((object)[
                "userId" => $response->id,
                "username" => $response->username
            ]);
        }
    }