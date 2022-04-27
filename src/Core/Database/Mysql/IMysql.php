<?php
    namespace Core\Database\Mysql;

    interface IMysql
    {
        function select(string $query, array $bind, string $model, string $fetchMode = \PDO::FETCH_OBJ) : object;

        function insert(string $query, array $bind) : int;
    }
?>