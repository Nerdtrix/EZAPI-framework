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


    public function validateOTPToken(int $otp): bool
    {
        $this->m_web2FAModel = $this->m_web2FaRepository->getByOtpId(token: $otp);

        if(empty($this->m_web2FAModel->id))
        {
            throw new ApiError("invalid_OTP");
        }


        //todo

        //validate time


        //delete

        
        return true;
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