<?php
    namespace Core\Mail;
    use Exception;
    use InvalidArgumentException;

    /**
     * @see https://github.com/Nerdtrix/EZMAIL
     * For issues with this SMTP library please see its own repository.
     */
    
    class EZMAIL implements IMailBuilderWriter
    {
        public string $appName = EZENV["APP_NAME"];
        public string $hostName = EZENV["SMTP_HOST"];
        public string $portNumber = EZENV["SMTP_PORT"];
        public float $timeout = 30;
        public int $authType = SMTP::AUTH_TYPE_STANDARD;
        public string $username = EZENV["SMTP_USERNAME"];
        public string $password = EZENV["SMTP_PASSWORD"];
        public string $authToken;
        public   bool $skipMessageIdValidation = true;

        #Message
        public string $subject = "";
        public array $from = [];
        public array $to = [];
        public array $cc = [];
        public array $bcc = [];
        public array $replyTo = [];
        public array $attachments = [];
        public string $bounceAddress = "";
    
        private ISMTPFactory $smtpFactory;
        private IMailIdGenerator $mailIdGenerator;
        private IMailBuilder $mailBuilder;    
        private ?ISMTP $smtp;


        public string $htmlTemplate = "";
        public string $body = "";

           
        public function __construct(ISMTPFactory $smtpFactory, IMailIdGenerator $mailIdGenerator, IMailBuilder $mailBuilder)
        {
            if(!empty(EZENV["SMTP_AUTH_TOKEN"]))
            {
                $this->authType = SMTP::AUTH_TYPE_2AUTH;
                $this->authToken = EZENV["SMTP_AUTH_TOKEN"];
            }
    
            $this->smtpFactory = $smtpFactory;
            $this->mailIdGenerator = $mailIdGenerator;
            $this->mailBuilder = $mailBuilder;  
        }
    
        private function validate() : void
        {
            if (empty($this->subject))
            {
                throw new InvalidArgumentException("Message subject is empty");
            }
    
            if (empty($this->body) && empty($this->htmlTemplate))
            {
                throw new InvalidArgumentException("Message body is empty");
            }
    
            if (empty($this->to))
            {
                throw new InvalidArgumentException("No message recipients");
            }
    
            if (empty($this->hostName))
            {
                throw new InvalidArgumentException("Hostname is empty");
            }
    
            if (empty($this->username))
            {
                throw new InvalidArgumentException("Username is empty");
            }
    
            if ($this->authType === SMTP::AUTH_TYPE_2AUTH)
            {
                if (empty($this->authToken))
                {
                    throw new InvalidArgumentException("Auth token is empty");
                }
            }
            else
            {
                if (empty($this->password))
                {
                    throw new InvalidArgumentException("Password is empty");
                }
            }
    
            if (count($this->from) > 1)
            {
                throw new InvalidArgumentException("Too many sender");
            }
        }
    
        public function send() : string
        {
            // Validating.
            $this->validate();
    
            // Creating SMTP instance.
            $this->smtp = $this->smtpFactory->create(
                $this->hostName,
                $this->portNumber,
                $this->timeout
            );
    
            try
            {
                // Connecting.
                $this->smtp->connect();
    
                // Do handshake.
                $this->smtp->doHandshake();
    
                // Authenticating.
                $useAuthToken = $this->authType === SMTP::AUTH_TYPE_2AUTH;
                $this->smtp->doAuth(
                    $this->username,
                    $useAuthToken ? $this->authToken : $this->password,
                    $this->authType
                );
                
                // Start mail session.
                $fromAddress = $this->username;
    
                if (!empty($this->from))
                {
                    $fromAddress = array_values($this->from)[0];
                }
    
                $toAddress = array_values($this->to);
                $this->smtp->startSendMail($fromAddress, $toAddress);
    
                // Sending mail data.
                $mailId = $this->mailIdGenerator->generate();
                $from = $this->from;
    
                if (empty($from))
                {
                    $from = [ $this->appName => $this->username ];
                }
    
                $replyTo = $this->replyTo;
    
                if (empty($replyTo))
                {
                    $replyTo = [ $this->username ];
                }
    
                $bounceAddress = $this->bounceAddress;
    
                if (empty($bounceAddress))
                {
                    $bounceAddress = $this->username;
                }


                #if html template is not empty load the template to the body instead.
                if(!empty($this->htmlTemplate))
                {
                    $template = sprintf("%s%sTemplates%s%s", dirname(__FILE__), SLASH, SLASH, $this->htmlTemplate);

                    if(!file_exists($template)) throw new Exception("The file location does not exists: {$template}");

                    #Start buffering
                    ob_start();

                    #Include the template
                    include($template);

                    #Assign the template's content to the body of the email protocol
                    $this->body =  ob_get_contents();

                    #End buffering
                    ob_end_clean ();
                }
    
                #Build email
                $this->mailBuilder->build(
                    $mailId,
                    $this->subject,
                    $this->body,
                    $from,
                    $this->to,
                    $this->cc,
                    $this->bcc,
                    $replyTo,
                    $this->attachments,
                    $bounceAddress,
                    $this->appName,
                    $this // will write back to $smtp.
                );
    
                // Done mail session.
                $mailIdResult = $this->smtp->endSendMail();
    
                if ($this->skipMessageIdValidation)
                {
                    $mailId = $mailIdResult;
                }
                else if ($mailId !== $mailIdResult)
                {
                    throw new Exception(sprintf(
                        "Unable to verify mail id. Expected: %s. Got: %s.",
                        $mailId,
                        $mailIdResult
                    ));
                }
    
                return $mailId;
            }
            finally
            {
                // Closing connection.
                $this->smtp->quit();
                unset($this->smtp);
            }
        }
    
        public function writeHeader(string $data): void
        {
            if ($this->smtp == null)
            {
                throw new Exception("SMTP not initialized");
            }
    
            $this->smtp->writeMailData($data);
        }
    
        public function writeBody(string $data): void
        {
            if ($this->smtp == null)
            {
                throw new Exception("SMTP not initialized");
            }
    
            $this->smtp->writeMailData($data);
        }
    }
?>