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
            \Core\ICrypto::class => \Core\Crypto::class,

            #Core DB
            \Core\Database\Mysql\IMysql::class => \Core\Database\Mysql\Mysql::class,
            
            #Core Mail
            \Core\Mail\ISMTPFactory::class => \Core\Mail\SMTPFactory::class,
            \Core\Mail\IMailIdGenerator::class => \Core\Mail\MailIdGenerator::class,
            \Core\Mail\IMailBuilder::class => \Core\Mail\MailBuilder::class,
            \Core\Mail\ILogger::class => \Core\Mail\EmptyLogger::class, //use the Logger class for debug
            \Core\Mail\IFileReader::class => \Core\Mail\FileReader::class,

            #language
            \Core\Language\ITranslator::class => \Core\Language\Translator::class,
            
            #Repositories
            \Repositories\IWeb2FARepository::class => \Repositories\Web2FARepository::class,
            \Repositories\ISessionRepository::class => \Repositories\SessionRepository::class,
            \Repositories\IUserRepository::class => \Repositories\UserRepository::class,
            \Repositories\IAuthRepository::class => \Repositories\AuthRepository::class,
            \Repositories\IDevicesRepository::class => \Repositories\DevicesRepository::class,

            #Services
            \Services\ISessionService::class => \Services\SessionService::class,
            \Services\IAuthService::class =>  \Services\AuthService::class,
            \Services\IMFAService::class => \Services\MFAService::class,
            \Services\IDevicesService::class => \Services\DevicesService::class,
            \Services\IUserService::class => \Services\UserService::class,
            \Services\IPasswordService::class => \Services\PasswordService::class
        ];
    }
?>