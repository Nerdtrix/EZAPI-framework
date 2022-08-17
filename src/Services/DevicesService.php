<?php
    namespace Services;
    use \stdClass;
    use Core\{IHelper, ICookie, ICrypto};
    use Core\Mail\EZMAIL;
    use Exception;
    use Models\DevicesModel;
    use Repositories\{IDevicesRepository};
    use Core\Mail\Templates\NewDevice\NewDeviceMail;
    use Core\Language\ITranslator;
    
    
    interface IDevicesService
    {
        function listDevicesByUserId(string $userId) : \stdClass;
        function addNewDevice(int $userId) : int;
        function sendNewDeviceDetectedEmail(string $name, string $email, string $locale) : void;

        function getDeviceId() : int;

    }
    
  class DevicesService implements IDevicesService
  {
    #Repositories
    private IDevicesRepository $m_devicesRepository;

    #Core
    private EZMAIL $m_email;
    private ICookie $m_cookie;
    private IHelper $m_helper;
    private ICrypto $m_crypto;
    private ITranslator $m_lang;
    
    #constant
    private const COOKIE_NAME = "device_token";

    #Model
    private DevicesModel $m_devicesModel;
    
  
    #Constructor
    public function __construct(
        IDevicesRepository $devicesRepository, 
        ICookie $cookie, 
        IHelper $helper, 
        ICrypto $crypto, 
        ITranslator $translator,
        EZMAIL $email)
    {
      $this->m_devicesRepository = $devicesRepository;
      $this->m_cookie = $cookie;
      $this->m_helper = $helper;
      $this->m_crypto = $crypto;
      $this->m_email = $email;
      $this->m_lang = $translator;
    }
    


    /**
     * 
     */
    public function listDevicesByUserId(string $userId) : stdClass
    {
      #find devices attached to the user
      return $this->m_devicesRepository->getDevicesByUserId(userId: $userId);
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
    public function addNewDevice(int $userId) : int
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
        return $this->m_devicesRepository->addNewDevice(
            userId: $userId, 
            ipAddress: $this->m_helper->publicIP(), 
            deviceName: $browserInfo->name, 
            cookieIdentifier: $randomHash,
            expiresAt: $sessionEnd
        );
    }

    
    /**
     * @param string name
     * @param string email
     * @param string locale
     * This method sends a new email to the user notifying that a new device has been added to his account. 
     */
    public function sendNewDeviceDetectedEmail(string $name, string $email, string $locale) : void
    {
        $this->m_email->to = [$name => $email];

        $this->m_email->subject = $this->m_lang->translate("login_from_new_device");

        $this->m_email->htmlTemplate = sprintf("NewDevice%sNewDeviceMail.phtml", SLASH);

        $brower = $this->m_helper->getBrowserInfo();

        #Fill template variables
        NewDeviceMail::$fName = $name;
        NewDeviceMail::$date = date("m/d/Y H:i:s", strtotime(TIMESTAMP));
        NewDeviceMail::$browser = $brower->name;
        NewDeviceMail::$platform = $brower->platform;
        NewDeviceMail::$ipAddress = $this->m_helper->publicIP();
      
        $this->m_email->send();
    }

  }
?>