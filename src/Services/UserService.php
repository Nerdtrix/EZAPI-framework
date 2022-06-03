<?php
    namespace Services;
    use Models\{UserModel};
    use Repositories\{IUserRepository};


    class UserService implements IUserService
    {
        private UserModel $m_userModel;
        private IUserRepository $m_userRepository;


        public function __construct(IUserRepository $userRepository)
        {
            $this->m_userRepository = $userRepository;
        }

        public function isLogged() : bool
        {
            return true;
        }

        public function userInfo() : object
        {
            return $this->m_userModel;
        }

        public function updateUser(){}

        public function editUser(){}

        public function deleteUser(){}
    }
?>