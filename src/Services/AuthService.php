<?php
  namespace Services;
  use Core\Exceptions\ApiError;
  
  use Models\{UserModel, UserAuthenticationModel};
  use Repositories\{IUserAuthenticationRepository, IUserRepository};
  
    
  class AuthService implements IAuthService
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

    #Password settings
    private const PSWCOUNTER = "attempts";//Cookie name
    private int $alertOnAttempts = 3; //Sends an email of failed login attempts
    private int $lockAccountOn = 5; //locks account on 5 failed login attempts and sends an email
    
  
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
     * @return UserModel object 
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

      #Check if the account is inactive
      if($this->m_userAuthModel->status == "blocked")
      {
        throw new ApiError ("account_inactive");
      }
      
      #Checking password input against password hash
      if(!password_verify($password, $this->m_userAuthModel->password))
      {
        #Record the amount of times the user tries to login.
        $failCount = 1;

        if($this->m_cookie->exits(self::PSWCOUNTER))
        {
          $failCount += (int)$this->m_cookie->get(self::PSWCOUNTER);
        }

        $this->m_cookie->set(
          name: self::PSWCOUNTER,
          value: $failCount,
          cookieExpiration: 0);

        if($failCount == $this->alertOnAttempts)
        {
          $this->m_deviceService->sendLoginAttempsEmail(
            name: $this->m_userAuthModel->fname, 
            email: $this->m_userAuthModel->email);
        }

        #lock account
        if($failCount == $this->lockAccountOn)
        {
          //TODO update account status to blocked

          #Send email
          $this->m_deviceService->sendAccountLockedEmail(
            name: $this->m_userAuthModel->fname, 
            email: $this->m_userAuthModel->email);

          #delete counter cookie
          $this->m_cookie->delete(self::PSWCOUNTER);
          
          throw new ApiError("account_locked_for_too_may_attempts");
        }

        throw new ApiError("invalid_username_or_password");
      }

      $isNewDevice = $this->m_deviceService->isNewDevice();

      if($isNewDevice || $this->m_userAuthModel->isTwoFactorAuth)
      {
        #Send a new OTP to the email address
        if($this->m_web2FaService->createOtpMailSessionToken(
          userInfo: $this->m_userAuthModel, 
          rememberMe: $rememberMe, 
          isNewDevice: $isNewDevice))
        {
          throw new ApiError ("otp_validation_required");
        }

        throw new ApiError ("unable_to_create_otp_session");
      }


      #Create session
      if(!$this->m_sessionService->create(userId: $this->m_userAuthModel->id, isValidated: true, rememberMe: $rememberMe))
      {
        throw new ApiError ("unable_to_authenticate");
      }

      if($isNewDevice)
      {
        #save device
        $this->m_deviceService->addNewDevice(userId: $this->m_userAuthModel->id);

        #send new device email
        $this->m_deviceService->sendNewDeviceDetectedEmail(
          name: $this->m_userAuthModel->fName, 
          email: $this->m_userAuthModel->email,
          locale: $this->m_userAuthModel->locale
        );
      }

      $this->m_userModel = $this->m_userRepository->getById(userId: $this->m_userAuthModel->id);

      return $this->m_userModel;
    }

    


    /**
     * @param int otp
     * @return UserModel object
     */
    public function verifyOTP(int $otp) : object 
    {
      #validate OTP
      $tokenObject = $this->m_web2FaService->validateOTP(otp: $otp);

      #Get user object
      $this->m_userModel = $this->m_userRepository->getById(userId: $tokenObject->userId);

      if($tokenObject->newDevice)
      {
        #save device
        $this->m_deviceService->addNewDevice(userId: $this->m_userModel->id);

        #send new device email
        $this->m_deviceService->sendNewDeviceDetectedEmail(
          name: $this->m_userModel->fName, 
          email: $this->m_userModel->email,
          locale: $this->m_userModel->locale
        );
      }

      #return user Object
      return $this->m_userModel;
    }


    
    public function resendOTP() : bool
    {
      return $this->m_web2FaService->resendOTPMail();
    }

    /**
     * getter
     */
    public function isLogged() : bool
    {
      return $this->m_sessionService->isValid();
    }


    public function endSession() : bool
    {
      if($this->m_sessionService->isValid())
      {
        return $this->m_sessionService->delete();
      }

      throw new ApiError("auth_requied");
    }
  }