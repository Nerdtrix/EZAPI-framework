<?php
    namespace Core\Mail;
    use Exception;
    use InvalidArgumentException;
    use Core\Mail\Templates\HtmlCompiler;

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

        #Mail template config using our own HTML compiler
        public string $locale = EZENV["DEFAULT_LOCALE"];
        public string $charset = "UTF-8";
        public string $title = EZENV["APP_NAME"];
        public string $header = "";
        public string $preHeader = "";
        public string $body = "";
        public string $footer = "";
        public string $htmlTemplate = "Default.html";
    
        public function __construct(ISMTPFactory $smtpFactory, IMailIdGenerator $mailIdGenerator, IMailBuilder $mailBuilder)
        {
            if(!empty(EZENV["SMTP_AUTH_TOKEN"]))
            {
                $this->ezmail->authType = SMTP::AUTH_TYPE_2AUTH;
                $this->ezmail->authToken = EZENV["SMTP_AUTH_TOKEN"];
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
    
            if (empty($this->body))
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

                #Compile the HTML template with the information provided.
                $body = HtmlCompiler::run([
                    "locale" => $this->locale,
                    "charset" => $this->charset,
                    "title" => $this->title, #optional
                    "header" => $this->header, #can also be html
                    "footer" => $this->footer, # Can also be html
                    "preHeader" => $this->preHeader,
                    "body" => $this->body
                ], $this->htmlTemplate);
    
                #Build email
                $this->mailBuilder->build(
                    $mailId,
                    $this->subject,
                    $body,
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