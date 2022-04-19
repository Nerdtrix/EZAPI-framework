<?php
    namespace Core\Database\Mysql;
    use Core\Constant;

    class MysqlQuery extends Mysql
    {
        public array $modelVariables = [];

        public function __construct(string $table)
        {
            parent::__construct($table);
        }


        /**
         * @method find
         * @param array  ex: ["id" => "value"]
         * @param string table obtional
         * @return array
         */
        public function find(array $array, string $table = null) : object
        {
            return $this->select([
                "where" => sprintf("%s= ?", key($array)),
                "bind" => [current($array)]
            ], $table);
        }


        /**
         * @method findFirst
         * @param array  ex: ["id" => "value"]
         * @param string table obtional
         * @return object
         */
        public function findFirst(array $array, string $table = null) : object
        {
            return $this->select([
                "where" => sprintf("%s= ?", key($array)),
                "bind" => [current($array)],
                "limit" => 1
            ], $table);
        }


        /**
         * @method findLast
         * @param array  ex: ["id" => "value"]
         * @param string table obtional
         * @return object
         */
        public function findLast(array $array, string $table = null) : object
        {
            return $this->select([
                "where" => sprintf("%s= ?", key($array)),
                "bind" => [current($array)],
                "limit" => 1,
                "order" => "id ASC"
            ], $table);
        }


        /**
         * @method search
         * @param array Ex: ["username" => "yourName"]
         * @param int limit
         * @param int offset
         * @param string order
         * @param string table obtional 
         * @return array
         */
        public function search(array $array, int $limit = 30, int $offset = 0, string $order = "id DESC", string $table = null) : object
        {
            return $this->select([
                "where" => key($array) . " like %?%",
                "bind" => [current($array)],
                "limit" => $limit,
                "offset" => $offset,
                "order" => $order
            ], $table);
        }


        /**
         * @method deleteId
         * @param int id
         * @param string table obtional 
         * @return array
         */
        public function deleteId(int $id, string $table = null) : array
        {
            return $this->delete([
                "where" => "id = ?", 
                "bind" => [$id]
            ], $table);
        }


        /**
         * @method deleteBy
         * @param array condition
         * @param string table obtional 
         * @return array
         */
        public function deleteBy(array $condition, string $table = null) : array
        {
            return $this->delete([
                "where" => key($condition) . " = ?", 
                "bind" => [end($condition)]
            ], $table);
        }


        /**
         * @method saveValues
         * @param array []
         * @param string table obtional 
         * @return boolean
         */
        public function saveValues(array $array, string $table = null) : bool 
        {
            $dateNames = [
                "date",
                "created",
                "createdAt",
                "created_at"
            ];

            #Build array
            $values = [];
            foreach($this->db->tableColumns($table) as $tableColumns)
            {
                #Hash password by default
                if($tableColumns->Field == "password")
                {
                    $values[$tableColumns->Field] = password_hash($array["password"], PASSWORD_DEFAULT);
                }
                else if(in_array($tableColumns->Field, $dateNames))
                {
                    $values[$tableColumns->Field] = TIMESTAMP;
                }
                else
                {
                    #Insert only Filled Values
                    if(!empty($array[$tableColumns->Field]))
                    {
                        $values[$tableColumns->Field] = $array[$tableColumns->Field];
                    }
                }
            }

            #Insert values
            if($this->insert($values, $table)) return true;

            return false;
        }


        /**
         * @method updateById
         * @param int id
         * @param array []
         * @param string table obtional 
         * @return string
         */
        public function updateById(int $id, array $array, string $table = null) : string
        {
            if(empty($id) || empty($array)) return "Empty values are not allowed";

            $tableFields = $this->tableFields($table);

            $set = null;
            $values = [];
            foreach($array as $key => $value)
            {
                if(!in_array($key, $tableFields))
                {
                return "Invalid value provided: {$key}";
                }

                if($key == "password")
                {
                    $values[] = password_hash($value, PASSWORD_DEFAULT);
                }
                else
                {
                    $values[] = $value;
                }

                $set .= "{$key} = ?,";
            }

            #Remove the last comma
            $set = rtrim($set, ',');

            #Add the id to the array
            array_push($values, $id);

            if($this->update([
                "set" => $set,
                "where" => $this->table === "user" || $table === "user" ? "id = ?" : "userId = ?",
                "bind" => $values
                ], $table)
            )

            return Constant::SUCCESS;
        }


        /**
         * @method updateBy
         * @param array updateBy
         * @param array array
         * @param string table obtional
         * @return string
         */
        public function updateBy(array $updateBy, array $array, string $table = null) : string
        {
            if(empty($array)) return "Empty values are not allowed";

            $tableFields = $this->tableFields($table);

            $set = null;
            $values = [];
            foreach($array as $key => $value)
            {
                if(!in_array($key, $tableFields))
                {
                return "Invalid value provided: {$key}";
                }

                if($key == "password")
                {
                    $values[] = password_hash($value, PASSWORD_DEFAULT);
                }
                else
                {
                    $values[] = $value;
                }

                $set .= "{$key} = ?,";
            }

            $set = rtrim($set, ',');

            array_push($values, end($updateBy));

            if($this->update([
                "set" => $set,
                "where" => sprintf("%s = ?", key($updateBy)),
                "bind" => $values
                ], $table)
            )

            return Constant::SUCCESS;
        }


        /**
         * @method tableFields
         * @param string table obtional
         * @return array
         */
        public function tableFields(string $table = null) : array
        {
            $fields = [];
            foreach($this->db->tableColumns($table) as $tableColumns)
            {
            $fields[] = $tableColumns->Field;
            }

            return $fields;
        }


        /**
         * @method save
         * @return bool
         */
        public function save() : bool
        {
            #Build array
            $values = [];
            foreach($this->tableColumns() as $tableColumns)
            {
                #Hash password by default
                if($tableColumns->Field === "password")
                {
                    $values[$tableColumns->Field] = password_hash($this->modelVariables[$tableColumns->Field], PASSWORD_DEFAULT);
                }
                else if($tableColumns->Field === "date" || $tableColumns->Field === "created" || $tableColumns->Field === "createdAT")
                {
                    $values[$tableColumns->Field] = TIMESTAMP;
                }
                else
                {
                    #Insert only Filled Values
                    if(!empty($this->modelVariables[$tableColumns->Field]))
                    {
                        $values[$tableColumns->Field] = $this->modelVariables[$tableColumns->Field];
                    }
                }
            }

            #Insert values
            if($this->insert($values)) return true;

            return false;
        }
    }