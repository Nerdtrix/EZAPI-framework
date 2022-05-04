<?php
    namespace Services;
    use Core\{ICookie, ICrypto};
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
        
        #constant
        private const USER_SESSION_NAME = "DNPMYRJZJENXA0BQGA5Q";
        private const USER_SESSION_EXPIRY = 20; #20 minutes

        
    
        #Constructor
        public function __construct(ICookie $cookie, ICrypto $crypto, ISessionRepository $sessionRepository)
        {
            $this->m_cookie = $cookie;
            $this->m_crypto = $crypto;
            $this->m_sessionRepository = $sessionRepository;
        }


      public function create(int $userId, bool $rememberMe = false) : bool
      {
          #Session random hash
          $sessionToken = $this->m_crypto->randomToken();
      
          $sessionEnd = 0;

          if($rememberMe)
          {
            $sessionEnd = time() + (self::USER_SESSION_EXPIRY * 60);
          }

          $sessions = $this->m_sessionRepository->listByUserId($userId);

         
          $create = $this->m_sessionRepository->create(
              userId: $userId,
              token: $sessionToken,
              expiresAt: $sessionEnd);

            if($create)
            {
               if($this->m_cookie->set(
                   name: self::USER_SESSION_NAME,
                   value: $sessionToken,
                   cookieExpiration: $sessionEnd))
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