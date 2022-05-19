<?php
    namespace Services;

use Core\Exceptions\ApiError;
use Core\Ihelper;
    use Models\Web2FAModel;
    use Repositories\IWeb2FARepository;
    
    
  class Web2FAService implements IWeb2FAService
  {
      #core
      private Ihelper $m_helper;

      #repositories
      private IWeb2FARepository $m_web2FaRepository;

      private Web2FAModel $m_web2FAModel;

      private const OTP_EXPIRATION_MINUTES = 15;

    public function __construct(Ihelper $helper, IWeb2FARepository $web2FaRepository)
    {
        $this->m_helper = $helper;

        $this->m_web2FaRepository = $web2FaRepository;
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

    public function sendOtpEmail(string $email) : bool
    {
        $otp = $this->m_helper->randomNumber(6);

        //send otp

        return true;
    }


    public function createOtpSessionToken(int $userId) : bool
    {
        return true;
    }

  }