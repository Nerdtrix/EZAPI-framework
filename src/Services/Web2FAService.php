<?php
    namespace Services;

    use Core\Exceptions\ApiError;
    use Core\Ihelper;
    use Models\Web2FAModel;
    use Repositories\{IWeb2FARepository, IUserRepository};
    use Core\Mail\EZMAIL;
    use Core\Mail\Templates\Web2FA\Web2FAMail;
    use Core\Language\ITranslator;
    
    
  class Web2FAService implements IWeb2FAService
  {
    private Ihelper $m_helper;
    private IWeb2FARepository $m_web2FaRepository;
    private ISessionService $m_sessionService;
    private IUserRepository $m_userRepository;
    private Web2FAModel $m_web2FAModel;
    private EZMAIL $m_email;
    private ITranslator $m_lang;
    private const OTP_EXPIRATION_MINUTES = 20;

    public function __construct(
        Ihelper $helper, 
        IWeb2FARepository $web2FaRepository, 
        IUserRepository $userRepository,
        EZMAIL $email, 
        ISessionService $sessionService,
        ITranslator $translator)
    {
        $this->m_helper = $helper;
        $this->m_userRepository = $userRepository;
        $this->m_web2FaRepository = $web2FaRepository;
        $this->m_email = $email;
        $this->m_sessionService = $sessionService;
        $this->m_lang = $translator;
    }

    public function createOtpMailSessionToken(object $userInfo, bool $rememberMe, bool $isNewDevice) : bool
    {
        #Create a random OTP
        $otp = $this->m_helper->randomNumber(6);

        #Minutes calculation
        $timestamp = CURRENT_TIME + (self::OTP_EXPIRATION_MINUTES * 60);

        #expiration time
        $expirationTime = date(DATE_FORMAT, $timestamp);  

        #Create invalidated session.
        if($this->m_sessionService->create(userId: $userInfo->id, isValidated: false, rememberMe: $rememberMe))
        {
            if(!$this->m_web2FaRepository->saveOtp(
                userId: $userInfo->id, 
                otp: $otp, 
                isNewDevice: $isNewDevice,
                expiresAt: $expirationTime))
            {
                throw new ApiError("unable_to_generate_OTP");
            }
        }
        
        #Send OTP email
        $this->sendOtpEmail(
            name: $userInfo->fName,
            email: $userInfo->email,
            locale: $userInfo->locale,
            otp: $otp
        );

        return true;
    }


    public function resendOTPMail() : bool
    {
        #Create a random OTP
        $otp = $this->m_helper->randomNumber(6);

        #Minutes calculation
        $timestamp = CURRENT_TIME + (self::OTP_EXPIRATION_MINUTES * 60);

        #expiration time
        $expirationTime = date(DATE_FORMAT, $timestamp); 

        if(!$this->m_web2FaRepository->updateOtpByUserId(
            userId: $this->m_sessionService->userId(), 
            otp: $otp, 
            expiresAt: $expirationTime))
        {
            throw new ApiError("unable_to_generate_OTP");
        }

        #user info
        $userInfo = $this->m_userRepository->getById(
                userId: $this->m_sessionService->userId());

        $this->sendOtpEmail(
            name: $userInfo->fName,
            email: $userInfo->email,
            locale: $userInfo->locale,
            otp: $otp
        );

        return true;
    }


    /**
     * @param string $name
     * @param string $email
     * @param string $locale
     * @param int $otp
     * This method sends a new email with the one time password to the user which expires in 20 minutes.
     */
    private function sendOtpEmail(string $name, string $email, string $locale, int $otp) : void
    {
        $this->m_email->to = [$name => $email];

        $this->m_email->subject = $this->m_lang->translate("your_one_time_verification_code");

        $this->m_email->htmlTemplate = sprintf("Web2FA%sWeb2FAMail.phtml", SLASH);

        #Fill template variables
        Web2FAMail::$otp = $otp;
      
        #Send mail
        $this->m_email->send();
    }



    /**
     * @param int otp
     * @return Web2FAModel
     * @throws ApiError
     */
    public function validateOTP(int $otp): Web2FAModel
    {
        #Find record
        $this->m_web2FAModel = $this->m_web2FaRepository->getByOtp(otp: $otp);

        if(empty($this->m_web2FAModel->id))
        {
            throw new ApiError("invalid_otp");
        }

        #Calculate expiration time
        $expirationTime = CURRENT_TIME + (self::OTP_EXPIRATION_MINUTES * 60);

        #Validate time
        if(strtotime(CURRENT_TIME) > $expirationTime)
        {
            throw new ApiError("expired_otp");
        }
        
        #update session
        if(!$this->m_sessionService->validateOtpSession(userId: $this->m_web2FAModel->userId))
        {
            throw new ApiError ("unable_to_authenticate");
        }

        #delete token
        $this->m_web2FaRepository->deleteByOtp(otp: $otp);

        #Validated
        return  $this->m_web2FAModel;

        throw new ApiError("unable_to_authenticate");
    }


   
  }

?>