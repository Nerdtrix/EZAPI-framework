<?php
  namespace Services;
  use \stdClass;
  use Core\Exceptions\ApiError;
  use Core\Helper;
  use Models\{UserModel, UserAuthenticationModel};
  use Repositories\{IDevicesRepository, IUserAuthenticationRepository, IUserRepository};
  
    
  class AuthenticationService implements IAuthenticationService
  {
    #Repositories
    private IUserAuthenticationRepository $m_authRepository;
    private IUserRepository $m_userRepository;
    private IDevicesRepository $m_devicesRepository;
    
    #Models
    private UserAuthenticationModel $m_userAuthModel;
    private UserModel $m_userModel;
    private stdClass $m_devicesModel;
    
  
    #Constructor
    public function __construct(
      IUserAuthenticationRepository $authRepository, 
      IUserRepository $userRepository, 
      IDevicesRepository $devicesRepository
    )
    {
      $this->m_authRepository = $authRepository;
      $this->m_userRepository = $userRepository;
      $this->m_devicesRepository = $devicesRepository;
    }
    


    /**
     * @param string usernameOrEmail
     * @param string password
     * @param bool rememberMe
     * @return object
     * @throws ApiError
     */
    public function authenticate(string $usernameOrEmail, string $password, bool $rememberMe) : object
    {

      #Find by email if provided a valid email
      if(filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL))
      {
        $this->m_userAuthModel = $this->m_authRepository->getUserByEmail(email: $usernameOrEmail);
      }
      else
      {
        $this->m_userAuthModel = $this->m_authRepository->getUserByUsername(username: $usernameOrEmail);
      }

      #User not found
      if(empty($this->m_userAuthModel->id))
      {
        throw new ApiError("user_not_found");
      }
      
      #Checking password input against password hash
      if(!password_verify($password, $this->m_userAuthModel->password))
      {
        throw new ApiError("invalid_username_or_password");
      }

      if($this->m_userAuthModel->status == "banned")
      {
        throw new ApiError ("account_banned");
      }

      if($this->m_userAuthModel->status == "inactive")
      {
        throw new ApiError ("account_inactive");
      }

      #find devices attached to the user
      $this->m_devicesModel = $this->m_devicesRepository->getDevicesByUserId(userId: $this->m_userAuthModel->id);

      if(count((array)$this->m_devicesModel) > 0)
      {
        $ip = (new Helper)->publicIP();
        //string deviceName = helper::getDeviceName();
        //match both with saved devices

        //if not match 2 auth required

        //if remember device save new device into the DB

        //skip 2 auth on saved devices when enabled

        //signed in from unknown device. 2 factor asked

        //send email on new device

        //send token on 2 auth
      } 


      

      //save session

      $this->m_userModel = $this->m_userRepository->getById(userId: $this->m_userAuthModel->id);


      return $this->m_userModel;
    }

   
  }