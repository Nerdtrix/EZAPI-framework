<?php
    namespace Core\Database\Mysql;

    interface IMysql
    {
        function db() : \PDO;

        function select(string $query, array $bind, string $model, string $fetchMode = \PDO::FETCH_OBJ) : object;

        function insert(string $query, array $bind) : int;

        function lastInsertId() : int;

        function update(string $query, array $bind) : bool;

        function delete(string $query, array $bind) : bool;

        const DATE_FORMAT = "Y-m-d";

        const DATE_TIME_FORMAT = "Y-m-d H:i:s";
    }
?>