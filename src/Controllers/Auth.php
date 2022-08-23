<?php
    namespace Controllers;
    use Core\{IRequest};
    use Attributes\Authorize;
    use Services\Auth\{IAuthService};
    use Core\Exceptions\ApiError;
    use Core\Language\ITranslator;
    use Exception;

    class Auth
    {
        private IAuthService $m_authService;
        private IRequest $m_request;
        private ITranslator $m_lang;


        public function __construct(
            IRequest $request, 
            IAuthService $authService, 
            ITranslator $translator)
        {
            $this->m_authService = $authService;
            $this->m_request = $request;
            $this->m_lang = $translator;
        }     


        /**
         * @api /auth/ or  /
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
         * @api /auth/Login
         * @method POST
         * @param object {usernameOrEmail: required string, password: required string, rememeberMe: obtional bool}
         * @return object
         * @throws ApiError
         */
        public function Login(object $input, string $method) : void
        { 
            if($this->m_authService->isLogged())
            {
                $response = $this->m_authService->getLoggedUserInfo();

                $this->m_request->response($response);
            }

            if($method == "PUT")
            {
                if(empty($input->otp))
                {
                    throw new ApiError("otp_is_required");
                }

                $response = $this->m_authService->verifyOTP(otp: (int)$input->otp);

                $this->m_request->response($response);
            }

            if(is_null($input))
            {
                throw new ApiError("Invalid_request_body");
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
         * @api /auth/logout
         * @method POST
         * @return object
         * @throws ApiError
         */
        #[Authorize]
        public function logout() : void 
        {
            $this->m_authService->endSession();

            $this->m_request->response("success");
        }




        /**
         * @method GET
         */
        public function resendOTP() : void
        {
            if($this->m_authService->isLogged())
            {
                throw new ApiError("already_logged");
            }
            
            if($this->m_authService->resendOTP())
            {
                $this->m_request->response("otp_sent");
            }
            
            throw new ApiError("unable_to_send_otp");
        }


        /**
         * @param object input
         * @param string method
         * @method POST Gets OTP email
         * @method PATCH Updates password
         * @method PUT updates password for logged users
         * @return object
         * @throws ApiError
         * @todo method get
         */
        public function passwordReset(object $input, string $method) : void
        {
            $response = null;

            if(is_null($input))
            {
                throw new Exception("Invalid request body");
            }

            if($method == "GET")
            {
                //todo: return list of available OTP
            }
            else if($method == "POST")
            {
                if(empty($input->email))
                {
                    throw new ApiError([
                        "field" => "email",
                        "message" => "email_is_required"]);
                }

                if(!$this->m_authService->requestOTPEmail($input->email))
                {
                    throw new ApiError("unable_to_send_otp");
                }

                $response = "otp_sent";

            }
            else if($method == "PATCH")
            {
                if(empty($input->otp))
                {
                    throw new ApiError("otp_is_required");
                }

                if($this->m_authService->changePassword($input, true))
                {
                    $response = "success";
                }
            }
            else if($method == "PUT")
            {
                if(!$this->m_authService->isLogged())
                {
                    throw new ApiError("auth_required");
                }

                if($this->m_authService->changePassword($input))
                {
                    $response = "success";
                }
            }

            if(is_null($response))
            {
                throw new ApiError("something_went_wrong");
            }

            $this->m_request->response($response);
        }

        /**
         * @api /auth/extend
         * @method POST or GET
         * @return object
         * @throws ApiError
         */
        #[Authorize]
        public function extend() : void 
        {
            if($this->m_authService->extendSession())
            {
                $this->m_request->response();
            }

            throw new ApiError("something_went_wrong");
        }


         /**
         * @method POST
         */
        public function register(object $input, string $method) : void 
        {
            if(is_null($input))
            {
                throw new Exception("Invalid request body");
            }

            if($method == "POST")
            {
                $this->m_authService->registerUser($input);
            }

            if($method == "PUT")
            {
                if(empty($input->otp))
                {

                }

                //otp
            }
        }
    }
?>