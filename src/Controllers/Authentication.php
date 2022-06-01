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
         * @param object $input {usernameOrEmail: string, password: string, rememeberMe: bool}
         * @return object
         * @throws ApiError
         */
        public function Login(object $input) : void
        { 

//             $mmg = "test";

// $doc = new \DOMDocument();
// $doc->loadHTML("<html><body>" . $mmg . "</body></html>");
// echo $doc->saveHTML();die;








                    #Create new dom document
//                     ob_start();

// include(SRC_DIR ."/Core/Mail/Templates/en_US/NewDevice.phtml");

// $file_content = ob_get_contents();

// print_r ($file_content);

// ob_end_clean ();

//                     #Looad the HTML data
//                   die;









            
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