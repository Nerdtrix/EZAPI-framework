<?php
  namespace Services;
  use Core\Exceptions\ApiError;
  use Models\{AuthModel, UserModel};
  use Repositories\{IAuthRepository, IUserRepository};


  interface IAuthService
  {
    function authenticate(
      string $usernameOrEmail, 
      string $password,
      bool $rememberMe) : object;
    
    
    
    function verifyOTP(int $otp) : object;

    function endSession() : bool;

    function resendOTP() : bool;

    function isLogged() : bool;

    function validateRegistrationFields(object $input) : void;

    function getLoggedUserInfo() : UserModel;
  }
    

  class AuthService implements IAuthService
  {
    #Repositories
    private IAuthRepository $m_authRepository;
    private IUserRepository $m_userRepository;
    
    #Models
    private UserModel $m_userModel;
    private AuthModel $m_authModel;

    #service
    private IDevicesService $m_deviceService;
    private IWeb2FAService $m_web2FaService;
    private ISessionService $m_sessionService;
    private IPasswordService $m_passwordService;

  
    #Constructor
    public function __construct(
      IAuthRepository $authRepository, 
      IUserRepository $userRepository,
      IDevicesService $deviceService,
      IWeb2FAService $web2faService,
      ISessionService $sessionService,
      IPasswordService $passwordService
    )
    {
      $this->m_authRepository = $authRepository;
      $this->m_userRepository = $userRepository;
      $this->m_deviceService = $deviceService;
      $this->m_web2FaService = $web2faService;
      $this->m_sessionService = $sessionService;
      $this->m_passwordService = $passwordService;
    }
    

    /**
     * @param string usernameOrEmail
     * @param string password
     * @param bool rememberMe
     * @return UserModel object 
     * @throws ApiError 
     */
    public function authenticate(
      string $usernameOrEmail, 
      string $password, 
      bool $rememberMe) : object
    {
      
      #Find by email or username
      $this->m_authModel = $this->m_authRepository->getUserByUsernameOrEmail($usernameOrEmail);

      #User not found
      if(empty($this->m_authModel->id))
      {
        throw new ApiError(ErrorMessage::INVALID_INPUT);
      }

      if($this->m_authModel->status == Status::BANNED)
      {
        throw new ApiError(ErrorMessage::ACCOUNT_BANNED);
      }

      if($this->m_authModel->status == Status::INACTIVE)
      {
        throw new ApiError(ErrorMessage::ACCOUNT_INACTIVE);
      }

      if($this->m_authModel->status == Status::BLOCKED)
      {
        throw new ApiError(ErrorMessage::ACCOUNT_BLOCKED);
      }

      #validate password
      $this->m_passwordService->validatePassword($password, $this->m_authModel);

      if($this->m_authModel->isTwoFactorAuth)
      {
        #Send a new OTP to the email address
        if($this->m_web2FaService->createOtpMailSessionToken(
          userInfo: $this->m_authModel, 
          rememberMe: $rememberMe))
        {
          throw new ApiError(ErrorMessage::OTP_VALIDATION_REQUIRED);
        }

        throw new ApiError(ErrorMessage::UNABLE_TO_CREATE_SESSION);
      }

      #Create session
      $sessionId = $this->m_sessionService->create(
        userId: $this->m_authModel->id, 
        isValidated: true, 
        rememberMe: $rememberMe);
        
      if($sessionId == 0)
      {
        throw new ApiError (ErrorMessage::UNABLE_TO_AUTHENTICATE);
      }

      if($this->m_sessionService->isNewDevice($sessionId))
      {
        #send new device email
        $this->m_deviceService->sendNewDeviceDetectedEmail(
          name: $this->m_authModel->fName, 
          email: $this->m_authModel->email,
          locale: $this->m_authModel->locale
        );

        #remove is new device status
        $this->m_sessionService->changeIsNewStatus($sessionId);
      }

      $this->m_userModel = $this->m_authRepository->getUserById($this->m_authModel->id);

      return $this->m_userModel;
    }


    /**
     * @method setter
     * @return bool
     * Delete the user's current session from both DB and cookie.
     */
    public function endSession() : bool
    {
      return $this->m_sessionService->delete();
    }


    /**
     * @method getter
     * @return bool
     * Verify if the current session is valid or not.
     */
    public function isLogged() : bool
    {
      return $this->m_sessionService->isValid();
    }


    /**
     * @method getter
     * @return UserModel
     * Get current logged user info.
     */
    public function getLoggedUserInfo() : UserModel
    {
      $this->m_userModel = $this->m_authRepository->getUserById($this->m_sessionService->userId());

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

      #return user Object
      return $this->m_userModel;
    }

    
    public function resendOTP() : bool
    {
      return $this->m_web2FaService->resendOTPMail();
    }

    
    

    /**
     * @param object input
     * @throws ApiError exceptions
     */
    public function validateRegistrationFields(object $input) : void
    {
      $errors = [];

      #use this format for validation
      // "errors" : [ {
      //   "field" : "phoneNumber",
      //   "message" : "Phone number already exists for another user."
      // } ],
      
      if(empty($input->fName)) $errors[] = ["fName" => "first_name_is_required"];

      if(empty($input->lName)) $errors[] = ["lName" => "last_name_is_required"];

      if(empty($input->username)) $errors[] = ["username" => "username_is_required"];

      if(empty($input->email)) $errors[] = ["email" => "email_is_required"];

      if(empty($input->password)) $errors[] = ["password" => "password_required"];

      if(empty($input->confirmPsw)) $errors[] = ["confirmPsw" => "confirm_password_is_required"];

      if(!empty($errors)) throw new ApiError($errors);
      
      // $userObj = $this->m_authRepository->getUserByEmail(email: $input->email);

      // if(!empty($userObj->id)) ["email" => "email_already_exits"];
    
      // $userObj = $this->m_authRepository->getUserByUsername(username: $input->username);
      
      // if(!empty($userObj->id)) ["username" => "username_already_exits"];

      // if(!empty($errors)) throw new ApiError($errors);



      
    }


  
  }