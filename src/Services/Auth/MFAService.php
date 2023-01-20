<?php
    namespace Services\Auth;
    use Core\Exceptions\ApiError;
    use Core\Ihelper;
    use Models\Auth\{MFAModel, AuthModel};
    use Repositories\Auth\IMFARepository;
    use Repositories\User\IUserRepository;
    use Core\Mail\EZMAIL;
    use Core\Mail\Templates\Web2FA\Web2FAMail;
    use Core\Language\ITranslator;
    
    interface IMFAService
    {
        function sendOTPEmail(AuthModel $userInfo) : bool;



        function createOtpMailSessionToken(object $userInfo, bool $rememberMe) : bool;
        function validateOTP(int $otp): int;
        function resendOTPMail() : bool;
    }
  class MFAService implements IMFAService
  {
    private Ihelper $m_helper;
    private IMFARepository $m_MFaRepository;
    private ISessionService $m_sessionService;
    private IUserRepository $m_userRepository;
    private MFAModel $m_web2FAModel;
    private EZMAIL $m_email;
    private ITranslator $m_lang;

    private const OTP_EXPIRATION_MINUTES = 20;

    public function __construct(
        Ihelper $helper, 
        IMFARepository $MFaRepository, 
        IUserRepository $userRepository,
        EZMAIL $email, 
        ISessionService $sessionService,
        ITranslator $translator)
    {
        $this->m_helper = $helper;
        $this->m_userRepository = $userRepository;
        $this->m_MFaRepository = $MFaRepository;
        $this->m_email = $email;
        $this->m_sessionService = $sessionService;
        $this->m_lang = $translator;
    }




    /**
     * @param AuthModel userInfo
     * @return bool
     */
    public function sendOTPEmail(AuthModel $userInfo) : bool
    {
        #Create a random OTP
        $otp = $this->m_helper->randomNumber(6);

        #Minutes calculation
        $expiration = CURRENT_TIME + (self::OTP_EXPIRATION_MINUTES * ONE_MINUTE);

        if(!$this->m_MFaRepository->saveOtp(
            userId: $userInfo->id, 
            otp: $otp, 
            expiresAt: $expiration))
        {
            throw new ApiError("unable_to_generate_OTP");
        }

        $this->sendEmail(
            name: $userInfo->fName,
            email: $userInfo->email,
            otp: $otp
        );

        return true;
    }


    /**
     * @param string name
     * @param string email
     * @param int opt
     */
    private function sendEmail(string $name, string $email, int $otp) : void
    {
        $this->m_email->to = [$name => $email];

        $this->m_email->subject = $this->m_lang->translate("your_one_time_verification_code");

        $this->m_email->htmlTemplate = sprintf("Web2FA%sWeb2FAMail.phtml", SLASH);

        #Fill template variables
        Web2FAMail::$fName = $name;
        Web2FAMail::$otp = $otp;
      
        #Send mail
        $this->m_email->send();
    }


    /**
     * @param int otp
     * @return int userId
     */
    public function validateOTP(int $otp): int
    {
        $this->m_web2FAModel = $this->m_MFaRepository->getByOtp(otp: $otp);

        if(empty($this->m_web2FAModel->id))
        {
            throw new ApiError(["fields" => [
                "otp" => "invalid_otp"
            ]]);
        }

        #Validate time
        if(strtotime($this->m_web2FAModel->expiresAt) >= CURRENT_TIME)
        {
            #delete token
            $this->m_MFaRepository->deleteByOtp(otp: $otp);

            #return userId
            return $this->m_web2FAModel->userId;
        }

        #delete token
        $this->m_MFaRepository->deleteByOtp(otp: $otp);

        throw new ApiError(["fields" => [
            "otp" => "expired_otp"
        ]]);
    }






    public function createOtpMailSessionToken(object $userInfo, bool $rememberMe) : bool
    {
        #Create a random OTP
        $otp = $this->m_helper->randomNumber(6);

        #Minutes calculation
        $expirationTime = CURRENT_TIME + (self::OTP_EXPIRATION_MINUTES * 60);

        #Create invalidated session.
        if($this->m_sessionService->create(
            userId: $userInfo->id, 
            isValidated: false, 
            rememberMe: $rememberMe))
        {
            if(!$this->m_MFaRepository->saveOtp(
                userId: $userInfo->id, 
                otp: $otp, 
                expiresAt: $expirationTime))
            {
                throw new ApiError("unable_to_generate_OTP");
            }
        }
        
        #Send OTP email
        $this->sendEmailOTP(
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

        if(!$this->m_MFaRepository->updateOtpByUserId(
            userId: $this->m_sessionService->userId(), 
            otp: $otp, 
            expiresAt: $expirationTime))
        {
            throw new ApiError("unable_to_generate_OTP");
        }

        #user info
        $userInfo = $this->m_userRepository->getById(
                userId: $this->m_sessionService->userId());

        $this->sendEmailOTP(
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
    private function sendEmailOTP(string $name, string $email, string $locale, int $otp) : void
    {
        $this->m_email->to = [$name => $email];

        $this->m_email->subject = $this->m_lang->translate("your_one_time_verification_code");

        $this->m_email->htmlTemplate = sprintf("Web2FA%sWeb2FAMail.phtml", SLASH);

        #Fill template variables
        Web2FAMail::$fName = $name;
        Web2FAMail::$otp = $otp;
      
        #Send mail
        $this->m_email->send();
    }

    public function sendSMSOTP()
    {
        //todo
    }

    public function sendCALLOTP()
    {
        //todo
    }



    /**
     * @param int otp
     * @return MFAModel
     * @throws ApiError
     */
    public function validateOTP1(int $otp): MFAModel
    {
        #Find record
        $this->m_web2FAModel = $this->m_MFaRepository->getByOtp(otp: $otp);

        if(empty($this->m_web2FAModel->id))
        {
            throw new ApiError("invalid_otp");
        }

        #Calculate expiration time
        $expirationTime = CURRENT_TIME + (self::OTP_EXPIRATION_MINUTES * 60);

        #Validate time
        if(strtotime(CURRENT_TIME) > $expirationTime)
        {
            #delete token
            $this->m_MFaRepository->deleteByOtp(otp: $otp);

            throw new ApiError("expired_otp");
        }
        
        #update session
        if(!$this->m_sessionService->validateOtpSession(userId: $this->m_web2FAModel->userId))
        {
            throw new ApiError ("unable_to_authenticate");
        }

        #delete token
        $this->m_MFaRepository->deleteByOtp(otp: $otp);

        #Validated
        return  $this->m_web2FAModel;

        throw new ApiError("unable_to_authenticate");
    }
  }

?>