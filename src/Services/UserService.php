<?php
    namespace Services;
    use Core\Exceptions\ApiError;
    use Models\{UserModel};
    use Repositories\{IUserRepository};


    class UserService implements IUserService
    {
        private UserModel $m_userModel;


        private IUserRepository $m_userRepository;

        private ISessionService $m_sessionService;


        public function __construct(
            IUserRepository $userRepository, 
            ISessionService $sessionService)
        {
            $this->m_userRepository = $userRepository;
            $this->m_sessionService = $sessionService;
        }

       
        public function userInfo() : object
        {
            if(!$this->m_sessionService->isValid())
            {
                throw new ApiError("auth_required");
            }

            $this->m_userModel = $this->m_userRepository->getById(
                userId: $this->m_sessionService->userId());

            return $this->m_userModel;
        }

        public function deleteUser(){}
    }
?>