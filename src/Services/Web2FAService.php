<?php
    namespace Services;

    use Core\Exceptions\ApiError;
    use Core\Ihelper;
    use Models\Web2FAModel;
    use Repositories\IWeb2FARepository;
    use Core\Mail\EZMAIL;
    use Core\Mail\Templates\Web2FA\Web2FAMail;
    
    
  class Web2FAService implements IWeb2FAService
  {
      #core
      private Ihelper $m_helper;

      #repositories
      private IWeb2FARepository $m_web2FaRepository;

      private Web2FAModel $m_web2FAModel;

      private EZMAIL $m_email;

      private const OTP_EXPIRATION_MINUTES = 20;

    public function __construct(Ihelper $helper, IWeb2FARepository $web2FaRepository, EZMAIL $email)
    {
        $this->m_helper = $helper;

        $this->m_web2FaRepository = $web2FaRepository;

        $this->m_email = $email;
    }

    public function createOtpMailSessionToken(int $userId) : bool
    {
        $otp = $this->m_helper->randomNumber(6);

        //todo
        //save otp
        //create cookie

        $this->sendOtpEmail(
            name: "",
            email: "",
            locale: "",
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

        if($locale == "en_US")
        {
            $this->m_email->subject = "Your one-time verification code";
        }
        else if($locale == "es_US")
        {
            $this->m_email->subject = "Su código de verificación de un solo uso";
        }        

        $this->m_email->htmlTemplate = sprintf("Web2FA%sWeb2FAMail.phtml", SLASH);

        #Fill template variables
        Web2FAMail::$otp = $otp;
        Web2FAMail::$locale = $locale;
      
        #Send mail
        $this->m_email->send();
    }



    /**
     * @param int otp
     * @return bool
     */
    public function validateOTPMailToken(int $otp): bool
    {
        #Find record
        $this->m_web2FAModel = $this->m_web2FaRepository->getByOtpId(token: $otp);

        if(empty($this->m_web2FAModel->id))
        {
            throw new ApiError("invalid_OTP");
        }

        $timestamp = CURRENT_TIME + (self::OTP_EXPIRATION_MINUTES * 60);
        $expirationTime = date(DATE_FORMAT, $timestamp);

        #Validate time
        if(strtotime($expirationTime) > strtotime(CURRENT_TIME))
        {
            throw new ApiError("OTP_expired");
        }

        #Validate and delete
        if($this->m_web2FAModel->token == $otp)
        {
            #delete token
            $this->m_web2FaRepository->deleteByOtpId(token: $otp);

            #Validated
            return true;
        }

        #Invalid
        return false;
    }

    //todo
    public function createOtpSMSSessionToken(int $userId) : bool
    {
        return true;
    }

    //todo
    public function validateOTPSMSToken(int $otp): bool
    {
        return true;
    }

    //todo
    public function createOtpAuthAPPSessionToken(int $userId) : bool
    {
        return true;
    }

    //todo
    public function validateOTPAuthAPPToken(int $otp): bool
    {
        return true;
    }
  }
?>