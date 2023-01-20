<?php
    namespace Repositories;
    use Core\Database\Mysql\IMysql;


    class Repositories
    {
        private IMysql $m_db;

        public function __construct(IMysql $mySql)
        {
            $this->m_db = $mySql;
        }


        public function autoInsert(object $rows, string $table) : int
        {
            $setValues = null;
        
            foreach($rows as $column => $value) 
            {
              $setValues .= "{$column}=?,";
            }

            $setValues = rtrim($setValues, ',');

            $query = "INSERT INTO {$table} SET {$setValues}";

            $stmt = $this->m_db->db()->prepare($query);

            $bindCount = 1;
            foreach($rows as $col => $val) 
            {
                $stmt->bindValue($bindCount, trim($val));

                $bindCount++;
            }

            if($stmt->execute())
            {
                return $this->m_db->lastInsertId();
            }

            return 0;
        }

        

        public function autoUpdate(object $rows, string $table, string $where, array $bind) : bool
        {
            $setValues = null;

            unset($rows->id);
        
            foreach($rows as $column => $value) 
            {
              $setValues .= "{$column}=?,";
            }

            $setValues = rtrim($setValues, ',');

            $query = "UPDATE {$table} SET {$setValues} WHERE {$where}";

            $stmt = $this->m_db->db()->prepare($query);

            $rows = array_merge((array)$rows, $bind);

            $bindCount = 1;
            foreach($rows as $col => $val) 
            {
                $stmt->bindValue($bindCount, trim($val));

                $bindCount++;
            }

            if($stmt->execute())
            {
                return true;
            }

            return false;
        }


        /**
         * @param class model
         * @param array values
         * @return stdClass
         * This method auto assign values to a model to easily insert properties that are filled.
         */
        public function assign(string $model, object $values) : object
        {
            #new Instance of a model
            $modelObject = new $model();

            #Propagate results
            foreach ($values as $key => $value) 
            {
                #only assign properties that are declared
                if (property_exists($modelObject, $key)) 
                {
                    $modelObject->$key = $value;
                }
            }

            return $modelObject;
        }
    }
?>