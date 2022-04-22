<?php
    namespace Core\Database\Mysql;

    interface IMysql
    {
        function execute(string $query, array $bind, string $parentClass) : object;
    }
?>