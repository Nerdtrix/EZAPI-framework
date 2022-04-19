<?php
  namespace Services;
  use Models\User;
  use Core\Exceptions\ApiError;
  use Repositories\IUserAuthRepository;
  
    
  class AuthService implements IAuthService
  {
    protected IUserAuthRepository $m_authRepository;
    protected User $m_user;

    public function __construct(IUserAuthRepository $authRepository)
    {
      $this->m_authRepository = $authRepository;
    }
    
    public function authenticate(string $usernameOrEmail, string $password, bool $rememberMe) : object
    {

      #Find by email if provided a valid email
      if(filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL))
      {
        $this->m_user = $this->m_authRepository->getUserByEmail($usernameOrEmail);
      }
      else
      {
        $this->m_user = $this->m_authRepository->getUserByUsername($usernameOrEmail);
      }

      #User not found
      if(empty($this->m_user->id))
      {
        throw new ApiError("user_not_found");
      }
      
      #Validate password
      if(!password_verify($password, $this->m_user->password))
      {
        throw new ApiError("invalid_username_or_password");
      }

      // if($this->m_user->status == "banned")
      // {
      //   throw new ApiError ("account_banned");
      // }

      // if($this->m_user->status == "inactive")
      // {
      //   throw new ApiError ("account_inactive");
      // }

      //remove password field
      unset($this->m_user->password);


      return $this->m_user;
    }

   
  }