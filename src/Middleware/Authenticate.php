<?php
    namespace Middleware;
    use Services\IAuthenticationService;

    class Authenticate
    {
        protected $auth;
        
        public function __construct(IAuthenticationService $auth)
        {
            $this->auth = $auth;
        }

        //In construction
    }
?>