<?php
    namespace Attributes;
    use Exception;
    use Core\DI;
    use Core\Exceptions\ApiError;
    use Services\Auth\{ISessionService};
    use Repositories\User\IUserRepository;
    

    #[\Attribute]
    class Authorize
    {
        private $m_di;

        private const ROLES = [
            "USER",
            "ADMIN"
        ];
        
        function __construct(mixed $roles = null)
        {
            #dependency injector
            $this->m_di = new DI();

            $session = $this->m_di->inject(ISessionService::class);

            if(!$session->isValid())
            {
                throw new ApiError("auth_required");
            }
            
            #Only validate role when required
            if(!is_null($roles))
            {
                $this->validateRole($roles);

                $userId = $session->userId();

                $this->validatePermission($userId, $roles);
            }
        }



        /**
         * @param int $userId
         * @param mixed $roles
         */
        private function validatePermission(int $userId, mixed $roles) : void
        {
            if($userId < 1) 
            {
                throw new ApiError("auth_required");
            }

            $userRepsitory = $this->m_di->inject(IUserRepository::class);

            $userInfo = $userRepsitory->getById($userId);

            if(is_array($roles))
            {
                if(!in_array($userInfo->role, $roles))
                {
                    throw new ApiError("permission_denied");
                }
            }
            else
            {
                if($roles != $userInfo->role)
                {
                    throw new ApiError("permission_denied");
                }
            }
        }



        /**
         * @param mixed $roles
         */
        private function validateRole(mixed $roles) : void
        {
            if(is_array($roles))
            {
                foreach($roles as $role)
                {
                    if(!in_array($role, self::ROLES))
                    {
                        throw new Exception("unknown role");
                    }
                }
            }
            else
            {
                if(!is_array($roles) && !in_array($roles, self::ROLES))
                {
                    throw new Exception("unknown role");
                }
            }
        }
    }
?>
