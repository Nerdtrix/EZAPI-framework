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
     */
    public function listDevicesByUserId(string $userId) : stdClass
    {
      #find devices attached to the user
      return $this->m_devicesRepository->getDevicesByUserId(userId: $userId);
    }


    public function isNewDevice() : bool
    {
        if(!$this->m_cookie->exists(self::COOKIE_NAME))
        {
            return true;
        }

        $this->m_devicesModel = $this->m_devicesRepository->getDeviceByCookieIdentifier(cookieIdentifier: $this->m_cookie->get(self::COOKIE_NAME));

        //todo match IP and name too

        if(!empty($this->m_devicesModel->id))
        {
            return true;
        }

        return false;

    }

    public function addNewDevice(int $userId) : void
    {
        $randomHash = $this->m_crypto->randomToken();

        $deviceName = "";

        //todo time


        if(!$this->m_cookie->set(name: self::COOKIE_NAME, value: $randomHash, cookieExpiration: 0))
        {
            throw new Exception("unable to save device");
        }

        $this->m_devicesRepository->addNewDevice(
            userId: $userId, 
            ipAddress: $this->m_helper->publicIP(), 
            deviceName: $deviceName, 
            cookieIdentifier: $randomHash
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