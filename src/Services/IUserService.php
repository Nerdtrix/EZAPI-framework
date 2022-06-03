<?php
    namespace Services;

    interface IUserService
    {
        function isLogged() : bool;
        function userInfo() : object;
    }
?>