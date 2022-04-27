<?php
    namespace Src;

    use Services\{IAuthenticationService, AuthenticationService, IWeb2FAService, Web2FAService, DevicesService, IDevicesService};
    use Repositories\{IUserRepository, UserRepository, IUserAuthenticationRepository, UserAuthenticationRepository, IDevicesRepository, DevicesRepository};
    use Core\Database\Mysql\{IMysql, Mysql};
    use Core\{ICookie, Cookie, IHelper, Helper};

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
            
            IMysql::class => Mysql::class,

            
        ];

    }