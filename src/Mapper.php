<?php
    namespace Src;

    use Core\Database\Mysql\{IMysql, Mysql};
    use Repositories\{IUserAuthRepository, UserAuthRepository};

    use Services\{
        IAuthService,
        AuthService
    };


    class Mapper
    {
        public static $map = [
            IAuthService::class =>  AuthService::class,
            IUserAuthRepository::class => UserAuthRepository::class,
            IMysql::class => Mysql::class
        ];

    }