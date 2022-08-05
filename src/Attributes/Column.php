<?php
    namespace Attributes;
    use Exception;

    #TO be used as a field validator
    class Column
    {
        public function __construct(
            string $type = "string", 
            int $length = 100, 
            bool $nullable = false,
            bool $unique = false)
        {
           //get called value
            $value = "";

            #Validate type
            if(gettype($value) != $type)
            {
                throw new Exception("");
            }

            #Validate length
            if(strlen($value) != $length){}

            #Validate nulll
            if(empty($value) && !$nullable){}
        }
    }
?>