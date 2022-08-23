<?php
    namespace Core\Mail;

    interface ISMTPFactory
    {
        public function create(string $hostName, int $portNumber, float $timeout = 30) : ISMTP;
    }

    class SMTPFactory implements ISMTPFactory
    {
        private ILogger $logger;

        public function __construct(ILogger $logger)
        {
            $this->logger = $logger;
        }

        public function create(
            string $hostName,
            int $portNumber,
            float $timeout = 30
        ) : ISMTP
        {
            return new SMTP(
                $hostName,
                $portNumber,
                $timeout,
                null, // Use default socket.
                $this->logger
            );
        }
    }
?>