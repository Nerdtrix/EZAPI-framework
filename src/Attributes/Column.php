<?php
    namespace Attributes;
    use Exception;

    #TO be used as a field validator
    class Column
    {
        public function __construct(
            string $type = "string", 
            int $minLength = 0, 
            int $maxLength = 150, 
            bool $isNullable = false)
        {
           //get called value
            $value = "";

            #Validate type
            if(gettype($value) != $type)
            {
                throw new Exception("invalid data type");
            }

            #Validate length
            if(strlen($value) < $minLength)
            {
                throw new Exception(
                    sprintf("the value length must be at least %s characters", $minLength));
            }

            if(strlen($value) > $maxLength)
            {
                throw new Exception(
                    sprintf("the value length must be at least %s characters", $maxLength));
            }

            #Validate nulll
            if(empty($value) && !$isNullable)
            {
                throw new Exception(sprintf("value is required"));
            }
        }


        //call the DB to dump values
    }
?>