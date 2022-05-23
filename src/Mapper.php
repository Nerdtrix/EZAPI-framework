<?php
    namespace Src;

    use Services\{IAuthenticationService, AuthenticationService, IWeb2FAService, Web2FAService, DevicesService, IDevicesService};
    use Repositories\{ISessionRepository, SessionRepository, IUserRepository, UserRepository, IUserAuthenticationRepository, UserAuthenticationRepository, IDevicesRepository, DevicesRepository};
    use Core\Database\Mysql\{IMysql, Mysql};
    use Core\{ICookie, Cookie, IHelper, Helper, ICrypto, Crypto};

    class Mapper
    {
        public static $map = [
            IAuthenticationService::class =>  AuthenticationService::class,

            IUserRepository::class => UserRepository::class,
            IUserAuthenticationRepository::class => UserAuthenticationRepository::class,
            IDevicesRepository::class => DevicesRepository::class,

            IWeb2FAService::class => Web2FAService::class,
            IDevicesService::class => DevicesService::class,


            ICookie::class => Cookie::class,
            IHelper::class => Helper::class,
            ICrypto::class => Crypto::class,
            
            IMysql::class => Mysql::class,

            ISessionRepository::class => SessionRepository::class,

            #Services
            \Services\ISessionService::class => \Services\SessionService::class,


            \Repositories\IWeb2FARepository::class => \Repositories\Web2FARepository::class,


            #Mail
            \Core\Mail\ISMTPFactory::class => \Core\Mail\SMTPFactory::class,
            \Core\Mail\IMailIdGenerator::class => \Core\Mail\MailIdGenerator::class,
            \Core\Mail\IMailBuilder::class => \Core\Mail\MailBuilder::class,
            \Core\Mail\ILogger::class => \Core\Mail\EmptyLogger::class, //use the Logger class for debug
            \Core\Mail\IFileReader::class => \Core\Mail\FileReader::class,

            #Language
            \Core\Language\ITranslator::class => \Core\Language\Translator::class,
            
        ];

    }