<?php
    namespace Middleware;
    use Services\IAuthService;

    class AuthMiddleware
    {
        protected $auth;
        
        public function __construct(IAuthService $auth)
        {
            $this->auth = $auth;
        }

        //In construction
    }
?>