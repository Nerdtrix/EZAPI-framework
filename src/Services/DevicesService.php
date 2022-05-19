<?php
    namespace Services;
    use \stdClass;
    use Core\Exceptions\ApiError;
    use Core\{IHelper, ICookie, ICrypto};
    use Exception;
    use Models\DevicesModel;
    use Repositories\{IDevicesRepository};
  
    
  class DevicesService implements IDevicesService
  {
    #Repositories
    private IDevicesRepository $m_devicesRepository;

    private $m_email;



    #Core
    private ICookie $m_cookie;
    private IHelper $m_helper;
    private ICrypto $m_crypto;
    
    #constant
    private const COOKIE_NAME = "device_token";

    #Model
    private DevicesModel $m_devicesModel;
    
  
    #Constructor
    public function __construct(IDevicesRepository $devicesRepository, ICookie $cookie, IHelper $helper, ICrypto $crypto)
    {
      $this->m_devicesRepository = $devicesRepository;
      $this->m_cookie = $cookie;
      $this->m_helper = $helper;
      $this->m_crypto = $crypto;
    }
    


    /**
     * 
     */
    public function listDevicesByUserId(string $userId) : stdClass
    {
      #find devices attached to the user
      return $this->m_devicesRepository->getDevicesByUserId(userId: $userId);
    }


    /**
     * @return bool
     * This method will verify if a cookie exits and find the db record 
     * using the cookie identifier and then validate the information.
     */
    public function isNewDevice() : bool
    {
        if(!$this->m_cookie->exists(self::COOKIE_NAME))
        {
            return true;
        }

        $this->m_devicesModel = $this->m_devicesRepository->getDeviceByCookieIdentifier(cookieIdentifier: $this->m_cookie->get(self::COOKIE_NAME));

        if(!empty($this->m_devicesModel->id))
        {
            #Get browser info
            $browserInfo = $this->m_helper->getBrowserInfo();

            #Get public Ip
            $publicIp = $this->m_helper->publicIP();

            #If everything match this is a valid device.
            if($this->m_devicesModel->ip === $publicIp && $this->m_devicesModel->name == $browserInfo->name)
            {
                return false;
            }
        }

        return true;
    }


    public function getDeviceId() : int
    {
        if(!$this->m_cookie->exists(self::COOKIE_NAME))
        {
            return 0;
        }

        $this->m_devicesModel = $this->m_devicesRepository->getDeviceByCookieIdentifier(cookieIdentifier: $this->m_cookie->get(self::COOKIE_NAME));

        if(!empty($this->m_devicesModel->id))
        {
            #Get browser info
            $browserInfo = $this->m_helper->getBrowserInfo();

            #Get public Ip
            $publicIp = $this->m_helper->publicIP();

            #If everything match this is a valid device.
            if($this->m_devicesModel->ip === $publicIp && $this->m_devicesModel->name == $browserInfo->name)
            {
                return $this->m_devicesModel->id;
            }
        }

        return 0;
    }


    /**
     * @param int userId
     * This method will generate a new token and assign it to a 
     * cookie and save the record in the database for 1 year.
     */
    public function addNewDevice(int $userId) : void
    {
        #Generate a random token
        $randomHash = $this->m_crypto->randomToken();

        #Get browser info
        $browserInfo = $this->m_helper->getBrowserInfo();

        #Set device token expiration time to 1 year
        $sessionEnd = date(DATE_FORMAT, strtotime('+1 year', CURRENT_TIME));

        #Set device token cookie
        if(!$this->m_cookie->set(
            name: self::COOKIE_NAME, 
            value: $randomHash, 
            cookieExpiration: $sessionEnd))
        {
            throw new Exception("Unable to save device");
        }

        #Save device info in the DB
        $this->m_devicesRepository->addNewDevice(
            userId: $userId, 
            ipAddress: $this->m_helper->publicIP(), 
            deviceName: $browserInfo->name, 
            cookieIdentifier: $randomHash,
            expiresAt: $sessionEnd
        );
    }

    public function sendNewDeviceDetectedEmail(string $email) : void
    {
        // $this->m_email->send(
        //     email: $email,
        //     subject: "A new device has been detected!",
        //     body: ""
        // );
    }

  }