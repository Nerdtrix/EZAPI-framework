<?php
    namespace Controllers;
    use Services\User\{IUserService};
    use Core\Exceptions\ApiError;
    use Core\{IRequest};
    use Attributes\Authorize;

    #[Authorize]
    class User
    {
        protected IUserService $m_userService;
        private IRequest $m_request;

        public function __construct(IRequest $request, IUserService $userService)
        {
            $this->m_userService = $userService;
            $this->m_request = $request;
        }  

        /** @api /user/info
         * @method GET
         * @return object
         * @throws ApiError
         */
        public function info() : void
        {
            $response = $this->m_userService->userInfo();

            $this->m_request->response($response);
        }

        
        public function update(object $input) : void
        {
            
        }

       
    }