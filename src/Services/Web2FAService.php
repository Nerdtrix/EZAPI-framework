<?php
    namespace Services;

    use Core\Exceptions\ApiError;
    use Core\Ihelper;
    use Models\Web2FAModel;
    use Repositories\IWeb2FARepository;
    use Core\Mail\EZMAIL;
    
    
  class Web2FAService implements IWeb2FAService
  {
      #core
      private Ihelper $m_helper;

      #repositories
      private IWeb2FARepository $m_web2FaRepository;

      private Web2FAModel $m_web2FAModel;

      private EZMAIL $m_email;

      private const OTP_EXPIRATION_MINUTES = 15;

    public function __construct(Ihelper $helper, IWeb2FARepository $web2FaRepository, EZMAIL $email)
    {
        $this->m_helper = $helper;

        $this->m_web2FaRepository = $web2FaRepository;

        $this->m_email = $email;
    }


    /**
     * @param int otp
     * @return bool
     */
    public function validateOTPToken(int $otp): bool
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

    public function sendOtpEmail(string $name, string $email) : void
    {
        $otp = $this->m_helper->randomNumber(6);

        //send otp

        $this->m_email->subject = "One-time Password";
        $this->m_email->body = "this is a test with your OTP: {$otp}";
        $this->m_email->to = [$name => $email]; //missing name $name, 

        $this->m_email->send();
    }


    public function createOtpSessionToken(int $userId) : bool
    {
        return true;
    }

  }