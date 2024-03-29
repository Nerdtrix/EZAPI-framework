<?php
    namespace Services\Auth;
    use Core\{ICookie, ICrypto};
    use Core\Exceptions\ApiError;
    use Models\Auth\{SessionModel, AuthModel};
    use Repositories\Auth\ISessionRepository;
    

    interface ISessionService
    {
        function create(int $userId, bool $isValidated, bool $rememberMe) : int;

        function isValid() : bool;

        function extend() : bool;

        function delete() : bool;

        function userId() : int;

        function isNewDevice(int $sessionId) : bool;

        function changeIsNewStatus(int $sessionId) : bool;

    }
    class SessionService implements ISessionService
    {
        #Core
        private ICookie $m_cookie;
        private ICrypto $m_crypto;

        #Models
        private SessionModel $m_sessionModel;
        private AuthModel $m_authModel;

        #repositories
        private ISessionRepository $m_sessionRepository;
        
        #Services
        private IDevicesService $m_deviceService;

        #constant
        private const USER_SESSION_NAME = EZENV["SESSION_COOKIE_NAME"];
        private const USER_SESSION_EXPIRY = 30; #30 minutes
        
    
        #Constructor
        public function __construct(
            ICookie $cookie, 
            ICrypto $crypto, 
            ISessionRepository $sessionRepository, 
            IDevicesService $deviceService)
        {
            $this->m_cookie = $cookie;
            $this->m_crypto = $crypto;
            $this->m_sessionRepository = $sessionRepository;
            $this->m_deviceService = $deviceService;       
        }


        /**
         * @param int $userId
         * @param bool $isValidated
         * @param bool $rememberMe
         * @return int
         */
        public function create(int $userId, bool $isValidated, bool $rememberMe) : int
        {
            #Session random hash
            $sessionToken = $this->m_crypto->randomToken();
            
            #Calculate expiration time
            $sessionEnd = CURRENT_TIME + (self::USER_SESSION_EXPIRY * ONE_MINUTE);   

            $sessions = $this->m_sessionRepository->listByUserId(userId: $userId);

            $deviceId = $this->m_deviceService->getDeviceId();

            $isNewDevice = $deviceId == 0 ? true : false;

            $activeSessions = 1;
            foreach($sessions as $session)
            {
                if(!empty($session->deviceId)  && is_object($session))
                {
                    if($sessionEnd  >= strtotime($session->expiresAt) && 
                    $session->deviceId > 0 &&
                    $session->deviceId != $deviceId && 
                    $session->isValidated)
                    {
                        $activeSessions++;
                    }
                }
            }
            
            #do not go over the maximun devices allowed
            if($activeSessions > (int)EZENV["MAX_DEVICES_ALLOWED"])
            {
                throw new ApiError("maximum_devices_logged");
            }
            
            #save device
            if($isNewDevice)
            {
                $deviceId = $this->m_deviceService->addNewDevice($userId);
            }

            $sessionId = $this->m_sessionRepository->create(
                userId: $userId,
                deviceId: $deviceId,
                isNewDevice: $isNewDevice,
                token: $sessionToken,
                isValidated: $isValidated,
                expiresAt: $sessionEnd);
         
            if($sessionId > 0)
            {
               if($this->m_cookie->set(
                   name: self::USER_SESSION_NAME,
                   value: $sessionToken,
                   cookieExpiration: $rememberMe == true ? $sessionEnd : 0))
                {
                    return $sessionId;
                }
            }
         
            return $sessionId;
        }

      

    
        /**
         * Verify if there is an active session.
         */
        public function isValid() : bool
        {
            #get current session hash
            $sessionToken = $this->m_cookie->get(self::USER_SESSION_NAME);

            if(is_null($sessionToken)) return false;

            $this->m_sessionModel = $this->m_sessionRepository->getBySessionToken($sessionToken);

            if(empty($this->m_sessionModel->id)) return false;

            if(!$this->m_sessionModel->isValidated || $this->m_sessionModel->deviceId == 0)
            {
                $this->delete();

                return false;
            }

            #Expiration time
            $sessionEnd = CURRENT_TIME + (self::USER_SESSION_EXPIRY * 60);

            #Validate expiration time             
            if($sessionEnd  >= strtotime($this->m_sessionModel->expiresAt))
            {
                return true;
            }
                
            $this->delete();

            return false;
        }


        /**
         * @return bool
         * Distroy current user session.
         */
        public function delete() : bool
        {
            $tokenToDistroy = $this->m_cookie->get(self::USER_SESSION_NAME);
                
            if(is_null($tokenToDistroy)) return false;

            if($this->m_cookie->delete(self::USER_SESSION_NAME))
            {
                if($this->m_sessionRepository->deleteByToken($tokenToDistroy))
                {
                    return true;
                }
            }

            return false;
        }


        /**
         * @method getter
         * @return int
         * get current logged userId
         */
        public function userId() : int
        {
            #get current session hash
            $sessionToken = $this->m_cookie->get(self::USER_SESSION_NAME);

            if(is_null($sessionToken)) throw new ApiError("auth_required");

            #Search in DB by session token
            $this->m_sessionModel = $this->m_sessionRepository->getBySessionToken($sessionToken);

            if(empty($this->m_sessionModel->id))throw new ApiError("auth_required");

            return $this->m_sessionModel->userId;
        }



        public function extend() : bool
        {
            $sessionToken = $this->m_cookie->get(self::USER_SESSION_NAME);

            if(is_null($sessionToken)) return false;

            #Search in DB by session name
            $this->m_sessionModel = $this->m_sessionRepository->getBySessionToken($sessionToken);

            #Verify if the session object is empty
            if(empty($this->m_sessionModel->id)) return false;

            #Validate session time to see if auth is required
            if(strtotime($this->m_sessionModel->expiresAt) <  CURRENT_TIME)
            {
                $this->m_sessionRepository->deleteById($this->m_sessionModel->id);

                return  false;
            }

            #Calculate expiration time
            $sessionEnd = CURRENT_TIME + (self::USER_SESSION_EXPIRY * ONE_MINUTE);

            #Extend session
            $this->m_sessionRepository->extendExpirationTime(
                time: $sessionEnd,
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

    

        


        /**
         * Getter
         * @param int sessionId
         * @return bool
         */
        public function isNewDevice(int $sessionId) : bool
        {
            $this->m_sessionModel = $this->m_sessionRepository->getById($sessionId);

            return $this->m_sessionModel->isNewDevice;
        }


        /**
         * Setter
         * @param int sessionId
         * @return bool
         */
        public function changeIsNewStatus(int $sessionId) : bool
        {
            return $this->m_sessionRepository->updateIsNewDevice($sessionId, false);
        }
  }
?>