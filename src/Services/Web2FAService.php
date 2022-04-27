<?php
    namespace Services;
  
    
  class Web2FAService implements IWeb2FAService
  {

    private function createOTP(): int
    {
        return 0;
    }

    public function validateOTPToken(string $otp): bool
    {
        //trhow api exception. invalid otp 
        //expired otp
        return true;
    }

    public function sendOtpEmail(string $email) : bool
    {
        $otp = $this->createOTP();

        //send otp

        return true;
    }

    public function sendNewDeviceDetected(string $email) : void
    {

    }


    

  }