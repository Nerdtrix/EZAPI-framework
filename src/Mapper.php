<?php
    namespace Src;

    class Mapper
    {
        public static $map = [

            #Core
            \Core\ICookie::class => \Core\Cookie::class,
            \Core\IHelper::class => \Core\Helper::class,
            \Core\ICrypto::class => \Core\Crypto::class,
            \Core\IRequest::class => \Core\Request::class,

            #Core DB
            \Core\Database\Mysql\IMysql::class => \Core\Database\Mysql\Mysql::class,
            
            #Core Mail
            \Core\Mail\ISMTPFactory::class => \Core\Mail\SMTPFactory::class,
            \Core\Mail\IMailIdGenerator::class => \Core\Mail\MailIdGenerator::class,
            \Core\Mail\IMailBuilder::class => \Core\Mail\MailBuilder::class,
            \Core\Mail\ILogger::class => \Core\Mail\EmptyLogger::class, //use the Logger class for debug
            \Core\Mail\IFileReader::class => \Core\Mail\FileReader::class,

            #Core Language
            \Core\Language\ITranslator::class => \Core\Language\Translator::class,
            
            #Repositories
            \Repositories\IWeb2FARepository::class => \Repositories\Web2FARepository::class,
            \Repositories\ISessionRepository::class => \Repositories\SessionRepository::class,
            \Repositories\IUserRepository::class => \Repositories\UserRepository::class,
            \Repositories\IUserAuthenticationRepository::class => \Repositories\UserAuthenticationRepository::class,
            \Repositories\IDevicesRepository::class => \Repositories\DevicesRepository::class,

            #Services
            \Services\ISessionService::class => \Services\SessionService::class,
            \Services\IAuthenticationService::class =>  \Services\AuthenticationService::class,
            \Services\IWeb2FAService::class => \Services\Web2FAService::class,
            \Services\IDevicesService::class => \Services\DevicesService::class,
        ];

    }