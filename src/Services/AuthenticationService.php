<?php
  namespace Services;
  use Core\Exceptions\ApiError;
  use Models\{UserModel, UserAuthenticationModel};
  use Repositories\{IUserAuthenticationRepository, IUserRepository};
  
    
  class AuthenticationService implements IAuthenticationService
  {
    #Repositories
    private IUserAuthenticationRepository $m_authRepository;
    private IUserRepository $m_userRepository;
    
    #Models
    private UserAuthenticationModel $m_userAuthModel;
    private UserModel $m_userModel;

    #service
    private IDevicesService $m_deviceService;
    private IWeb2FAService $m_web2FaService;
    private ISessionService $m_sessionService;
    
  
    #Constructor
    public function __construct(
      IUserAuthenticationRepository $authRepository, 
      IUserRepository $userRepository,
      IDevicesService $deviceService,
      IWeb2FAService $web2faService,
      ISessionService $sessionService
    )
    {
      $this->m_authRepository = $authRepository;
      $this->m_userRepository = $userRepository;
      $this->m_deviceService = $deviceService;
      $this->m_web2FaService = $web2faService;
      $this->m_sessionService = $sessionService;
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

      #Check if the account is banned
      if($this->m_userAuthModel->status == "banned")
      {
        throw new ApiError ("account_banned");
      }

      #Check if the account is inactive
      if($this->m_userAuthModel->status == "inactive")
      {
        throw new ApiError ("account_inactive");
      }

      $isNewDevice = $this->m_deviceService->isNewDevice();

      if($isNewDevice || $rememberMe && $this->m_userAuthModel->isTwoFactorAuth)
      {
        // #Send a new OTP to the email address
        // if($this->m_web2FaService->createOtpSessionToken(userId: $this->m_userAuthModel->id))
        // {
        //   throw new ApiError ("otp_validation_required");
        // }

        // throw new ApiError ("unable_to_create_OTP_session");
      }

      
      if($isNewDevice)
      {
        #save device
        $this->m_deviceService->addNewDevice(userId: $this->m_userAuthModel->id);

        #send new device email
        $this->m_deviceService->sendNewDeviceDetectedEmail(
          name: $this->m_userAuthModel->fName, 
          email: $this->m_userAuthModel->email
        );
      }


      #Create session
      if(!$this->m_sessionService->create(userId: $this->m_userAuthModel->id, rememberMe: $rememberMe))
      {
        throw new ApiError ("unable_to_authenticate");
      }

      $this->m_userModel = $this->m_userRepository->getById(userId: $this->m_userAuthModel->id);

      return $this->m_userModel;
    }

   
  }