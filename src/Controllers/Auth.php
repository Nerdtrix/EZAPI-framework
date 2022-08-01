<?php
    namespace Controllers;
    use Core\{IRequest};
    use Attributes\Authorize;
    use Services\{IAuthService};
    use Core\Exceptions\ApiError;
    use Core\Language\ITranslator;
    use Exception;

    class Auth
    {
        private IAuthService $m_authService;
        private IRequest $m_request;
        private ITranslator $m_lang;


        public function __construct(IRequest $request, IAuthService $authService, ITranslator $translator)
        {
            $this->m_authService = $authService;
            $this->m_request = $request;
            $this->m_lang = $translator;
        }     


        /**
         * @api route /auth/ or  /
         * @method GET
         * @return object
         */
        public function index() : void
        {  
            $this->m_request->response((object)[
                "greeting" => $this->m_lang->translate("hello_world"),
                "app_name" => EZENV["APP_NAME"],
                "version" => EZENV["APP_VERSION"]
            ]);
        }
      
        
        /**
         * @api route /auth/Login
         * @method POST
         * @param object $input {usernameOrEmail: required string, password: required string, rememeberMe: obtional bool}
         * @return object
         * @throws Exception
         * @throws ApiError
         */
        public function Login(object $input) : void
        { 
            if($this->m_authService->isLogged())
            {
                throw new ApiError("already_logged");
            }

            if(is_null($input))
            {
                throw new Exception("Invalid request body");
            }
            
            if(empty($input->usernameOrEmail))
            {
                throw new ApiError("username_or_email_is_required");
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

            $this->m_request->response($response);
        }


        /**
         * @api route /auth/web2fa
         * @method post
         * @param object $input {otp: int}
         * @return object
         * @throws ApiError
         */
        public function web2fa(object $input) : void
        {
            if(is_null($input))
            {
                throw new Exception("Invalid request body");
            }
            
            if(empty($input->otp))
            {
                throw new ApiError("otp_is_required");
            }

            $response = $this->m_authService->verifyOTP(otp: (int)$input->otp);

            $this->m_request->response($response);
        }


        /**
         * @method GET
         */
        public function resendOTP() : void
        {
            if($this->m_authService->resendOTP())
            {
                $this->m_request->response("otp_sent");
            }
            
            throw new ApiError("unable_to_send_otp");
        }


        public function register() : void 
        {

        }

        /**
         * @method GET or POST
         * @return string
         * @throws ApiError
         * [Authorize(Roles = "admin, superuser")]
         */


        //#[Route("/logout", "POST")]
        #[Authorize]
        public function logout() : void 
        {
            if($this->m_authService->endSession())
            {
                $this->m_request->response("success");
            }
            
            throw new ApiError("session_expired");
        }


        

        #[Authorize]
        public function extend() : void 
        {
            
        }
    }
?>