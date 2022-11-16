<?php
    namespace Services\User;
    use Services\Auth\ISessionService;
    use Core\Exceptions\ApiError;
    use Models\User\UserModel;
    use Repositories\User\IUserRepository;

    interface IUserService
    {
        function userInfo() : object;
    }

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
            $this->m_userModel = $this->m_userRepository->getById(
                userId: $this->m_sessionService->userId());

            return $this->m_userModel;
        }

    }
?>