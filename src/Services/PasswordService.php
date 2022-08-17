<?php
    namespace Services;
    use Core\{ICookie, IHelper, ICrypto};
    use Core\Exceptions\ApiError;
    use Core\Mail\EZMAIL;
    use Models\AuthModel;
    use Core\Language\ITranslator;
    use Repositories\IAuthRepository;
    use Core\Mail\Templates\LoginAttempt\LoginAttempt;
    use Core\Mail\Templates\AccountLocked\AccountLocked;
    
    interface IPasswordService
    {
        function validatePassword(string $inputPassword, AuthModel $authModel) : void;
    }

    class PasswordService implements IPasswordService
    {
        private IAuthRepository $m_authRepository;

        private Icookie $m_cookie;
        private EZMAIL $m_email;
        private ITranslator $m_lang;
        private IHelper $m_helper;
        private ICrypto $m_crypto;

        #Password settings
        private const PSWCOUNTER = "attempts";//Cookie name
        private int $alertOnAttempts = 3; //Sends an email of failed login attempts
        private int $lockAccountOn = 5; //locks account on 5 failed login attempts and sends an email

        public function __construct(
            Icookie $cookie, 
            EZMAIL $email, 
            ITranslator $translator, 
            IHelper $helper,
            IAuthRepository $authRepository,
            ICrypto $crypto)
        {
            $this->m_cookie = $cookie;
            $this->m_email = $email;
            $this->m_lang = $translator;
            $this->m_helper = $helper;
            $this->m_authRepository = $authRepository;
            $this->m_crypto = $crypto;
        }

        
        /**
         * @param string inputPassword
         * @param AuthModel authModel
         * Here we validate if the user has entered the correct password or not. 
         * Later we will add MD5 and other protection techniques such as salt to this function.
         */
        public function validatePassword(string $inputPassword, AuthModel $authModel) : void
        {
            #Decrypt the encrypted password
            $decryptedPassword = $this->m_crypto->AESDecrypt($authModel->password);

            #Checking password input against password hash
            if(!password_verify($inputPassword, $decryptedPassword))
            {
                $this->recordfailAttempt($authModel);

                throw new ApiError(ErrorMessage::INVALID_INPUT);
            }
        }


        /**
         * Record the amount of times the user entered the wrong password.
         */
        private function recordfailAttempt(AuthModel $authModel) : void
        {
            #Record the amount of times the user tries to login.
            $failCount = 1;

            if($this->m_cookie->exists(self::PSWCOUNTER))
            {
                $cookieCount = (int)$this->m_cookie->get(self::PSWCOUNTER);

                if($cookieCount < $this->lockAccountOn)
                {
                    $failCount += $cookieCount;
                }
                else
                {
                    $this->m_cookie->delete(self::PSWCOUNTER);
                }
            }

            $this->m_cookie->set(
                name: self::PSWCOUNTER,
                value: $failCount,
                cookieExpiration: 0);

            if($failCount == $this->alertOnAttempts)
            {
                $this->sendLoginAttempsEmail(
                    name: $authModel->fName, 
                    email: $authModel->email);
            }

            #lock account
            if($failCount == $this->lockAccountOn)
            {
                #Update user status
                $this->m_authRepository->updateUserStatus(
                    userId: $authModel->id,
                    status: STATUS::BLOCKED
                );

                #Send email
                $this->sendAccountLockedEmail(
                    name: $authModel->fName, 
                    email: $authModel->email);

                #delete counter cookie
                $this->m_cookie->delete(self::PSWCOUNTER);
                
                throw new ApiError(ErrorMessage::TOO_MANY_ATTEMPTS);
            }
        }


        /**
         * @param string name
         * @param string email
         * @send a warning email when the user reach the limit specified. 
         */
        private function sendLoginAttempsEmail(string $name, string $email) : void
        {
            $this->m_email->to = [$name => $email];

            $this->m_email->subject = $this->m_lang->translate("too_many_login_attempts");
      
            $this->m_email->htmlTemplate = sprintf("LoginAttempt%sLoginAttemptMail.phtml", SLASH);
      
            $brower = $this->m_helper->getBrowserInfo();
      
            #Fill template variables
            LoginAttempt::$fName = $name;
            LoginAttempt::$date = date("m/d/Y H:i:s", strtotime(TIMESTAMP));
            LoginAttempt::$browser = $brower->name;
            LoginAttempt::$platform = $brower->platform;
            LoginAttempt::$ipAddress = $this->m_helper->publicIP();
          
            #Send mail
            $this->m_email->send();
        }

        
        /**
         * @param string name
         * @param string email
         * Send an email when the user reach a the limit of tries allowed.
         */
        private function sendAccountLockedEmail(string $name, string $email): void
        {
            $this->m_email->to = [$name => $email];

            $this->m_email->subject = $this->m_lang->translate("account_locked");

            $this->m_email->htmlTemplate = sprintf("AccountLocked%sAccountLockedMail.phtml", SLASH);

            AccountLocked::$fName = $name;
            
            #Send mail
            $this->m_email->send();
        }
    }
?>