<?php
    namespace Services;
    use Core\{EZENV, ICookie, ICrypto};
    use Core\Exceptions\ApiError;
    use Exception;
    use Models\SessionModel;
    use Repositories\ISessionRepository;
    

  class SessionService implements ISessionService
  {
        #Core
        private ICookie $m_cookie;
        private ICrypto $m_crypto;

        #repositories
        private ISessionRepository $m_sessionRepository;

        private SessionModel $m_sessionModel;
        
        private IDevicesService $m_deviceService;

        #constant
        private const USER_SESSION_NAME = "EZAPI_DNPMYRJZJENXA0BQGA5Q";
        private const USER_SESSION_EXPIRY = 20; #20 minutes

        
    
        #Constructor
        public function __construct(ICookie $cookie, ICrypto $crypto, ISessionRepository $sessionRepository, IDevicesService $deviceService)
        {
            $this->m_cookie = $cookie;
            $this->m_crypto = $crypto;
            $this->m_sessionRepository = $sessionRepository;

            $this->m_deviceService = $deviceService;
            
        }


        /**
         * @param int $userId
         * @param bool $rememberMe
         * @return bool
         */
        public function create(int $userId, bool $rememberMe) : bool
        {
            #Session random hash
            $sessionToken = $this->m_crypto->randomToken();

            
            $timestamp = CURRENT_TIME + (self::USER_SESSION_EXPIRY * 60);

            $sessionEnd = date(DATE_FORMAT, $timestamp);      
            

            $sessions = $this->m_sessionRepository->listByUserId(userId: $userId);

            $loggedDevices = 0;
            foreach($sessions as $session)
            {
                if(is_object($session))
                {

                    
                    //todo validate date


                    $loggedDevices++;
                }
            }


          if($loggedDevices > (int)EZENV["MAX_DEVICES_ALLOWED"])
          {
            throw new ApiError("Maximun_devices_logged");
          }


         
          $create = $this->m_sessionRepository->create(
              userId: $userId,
              deviceId: $this->m_deviceService->getDeviceId(),
              token: $sessionToken,
              expiresAt: $sessionEnd);

            if($create)
            {
               if($this->m_cookie->set(
                   name: self::USER_SESSION_NAME,
                   value: $sessionToken,
                   cookieExpiration: $rememberMe ? $sessionEnd : 0))
                {
                    return true;
                }
            }
         
          return false;
      }

      

      public function isValid() : bool
      {
        if(!$this->m_cookie->exists(self::USER_SESSION_NAME))
        {
            return false;
        }

         #get current session hash
         $sessionToken = $this->m_cookie->get(self::USER_SESSION_NAME);

         #Search in DB by session token
         $this->m_sessionModel = $this->m_sessionRepository->getBySessionToken(token: $sessionToken);

        if(empty($this->m_sessionModel->id))
        {
            return false;
        }

        #If not empty validate session and return userId                
        if(!empty($session) && strtotime($session->expiresAt) >=  time())
        {
            return true;
        }
      }

        public function extend() : bool
        {
            if(!$this->m_cookie->exists(self::USER_SESSION_NAME))
            {
                return false;
            }

            #get current session hash
            $sessionToken = $this->m_cookie->get(self::USER_SESSION_NAME);

            #Search in DB by session name
            $this->m_sessionModel = $this->m_sessionRepository->getBySessionToken(token: $sessionToken);

            #Verify if the session object is empty
            if(empty($this->m_sessionModel->id))
            {
                return false;
            }

            #Validate session time to see if auth is required
            if(strtotime($this->m_sessionModel->expiresAt) <  time())
            {
                #delete session
                $this->m_sessionRepository->deleteById(sessionId: $this->m_sessionModel->id);

                return  false;
            }

            #Calculate expiration time
            $sessionEnd = time() + (self::USER_SESSION_EXPIRY * 60);

            #Extend session
            $this->m_sessionRepository->extendExpirationTime(
                time: date($this->m_sessionRepository->getDateTimeFormat(), $sessionEnd),
                sessionToken: $this->m_sessionModel->token
            );
            
            #Set Cookie 
            if(!$this->m_cookie->set(
                name: self::USER_SESSION_NAME,
                value: $this->m_sessionModel->token,
                cookieExpiration: $sessionEnd))
            {
                return false;
            }

            #Done
            return true;
        }

    

        public function delete() : bool
        {

            if($this->m_cookie->exists(self::USER_SESSION_NAME))
            {
                if($this->m_cookie->delete(self::USER_SESSION_NAME))
                {
                    $tokenToDistroy = $this->m_cookie->get(self::USER_SESSION_NAME);

                    if($this->m_sessionRepository->deleteByToken(token: $tokenToDistroy))
                    {
                        return true;
                    }
                }

                return false;
            }

            return false;
        }
  }