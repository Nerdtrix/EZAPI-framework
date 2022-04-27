<?php
    namespace Src;

    use Services\{IAuthenticationService, AuthenticationService};
    use Repositories\{IUserRepository, UserRepository, IUserAuthenticationRepository, UserAuthenticationRepository, IDevicesRepository, DevicesRepository};
    use Core\Database\Mysql\{IMysql, Mysql};

    class Mapper
    {
        public static $map = [
            IAuthenticationService::class =>  AuthenticationService::class,

            IUserRepository::class => UserRepository::class,
            IUserAuthenticationRepository::class => UserAuthenticationRepository::class,
            IDevicesRepository::class => DevicesRepository::class,

            
            IMysql::class => Mysql::class,

            
        ];

    }