<?php
    namespace Core;
<<<<<<< HEAD
    use Core\Constant;
    use \SplFileObject;
    use \Exception;
    use \ReflectionMethod;


     /**
    * @comment Dynamic Dependency Injection
    * The idea of using dependency injection is to create new instance without having to worry 
    * about doing so manually. With this class we can automatically guess if the class needs a 
    * new instance or not. To better use this class create classes with static methods separated
    * we usually skip classes with static methods for performance. 
    */


    class DI
    {
        private $initialize = [];

        /**
         * @method load
         * @param string className
         * @throws exception
         * @return object
         * This function will scan the class from line 0 to line class and 
         * get any USE dependencies from that class to keep it in an array.
         */
        public function load(string $className) : object 
        {
            $filePath = sprintf(
                "%s%s%s.php", 
                dirname(__DIR__), 
                SLASH, 
                str_replace("\\", SLASH, $className) #Replace slash with enviromental slash
            );
            
            
            #Check whether the file exists and if it is not in the exceptions folder
            if(file_exists($filePath))
            {
                $file = new SplFileObject($filePath);
                $blacklisted = null;
            
                while (!$file->eof())
                {
                    $line = $file->fgets();

                    /**
                     * It is very important to break the loop right when the line starts with the 
                     * word 'class' to prevent the file lines to get in temp memory and become 
                     * a security risk. It will also help with performace since we only need 
                     * those lines before the class.
                     */
                    if (preg_match('#class#', $line)) 
                    {
                        //We do not need to create new instance of extends.
                        $alias = explode("extends ", $line);

                        //blacklisted resource 
                        $blacklisted = trim(!empty($alias[1]) ? $alias[1] : $alias[0]);

                        //last line
                        break;
                    } 

                    //explode by use word
                    $alias = explode("use ", $line);

                    #Skip exceptions from being instantiated.
                    if(!empty($alias[1]) && strpos($alias[1], "Core\Exceptions") === false)
                    {
                        //Remove semicolons and spaces
                        $trace = trim(str_replace(";", "", $alias[1]));

                        //Detecting our calling key
                        if(preg_match('#as#', $trace))
                        {
                            //Explode by as
                            $ins = explode("as", $trace);

                            $trace = $ins[0];
                        }
                        else
                        {                            
                            //Explode by backslash
                            $ins = explode("\\", $trace);
                        }

                        //Skip any PHP default classes
                        if(!empty($ins[0]))
                        {
                            //Get class methods and assign it them to their key name
                            $this->initialize[end($ins)] = $trace;
                        }
                    }
                } 

                $file = null; //Unset the file to prevent memory leaks

                if(!empty($blacklisted))
                {
                    //remove blacklisted
                    unset($this->initialize[$blacklisted]);
                }

                //Instantiate
                $this->methods();

                //Return object
                return (object) $this->initialize;
            }

            if(!EZENV["PRODUCTION"])
            {
                die (sprintf("%s: %s", Constant::UNABLE_TO_FIND_PATH, $filePath));
            }

            throw new Exception (sprintf("%s: %s", Constant::UNABLE_TO_FIND_PATH, $filePath));
        }


        /**
         * @method methods
         * in this method we initialize What is needed
         */
        private function methods() : void
        {
            $methodList = null;
            foreach($this->initialize as $class => $trace)
            {
                //get list of methods in each class
                $methods = get_class_methods($trace);

                $list = null;

                //check if the method is static or not
                foreach($methods as $checkInstance)
                {
                    //Skip constructor and destructor add any other here
                    if($checkInstance != "__construct" || $checkInstance != "__destruct")
                    {
                        //Check wether the method is static or not
                        $MethodChecker = new ReflectionMethod($trace, $checkInstance);
                        $list[$checkInstance] = $MethodChecker->isStatic() ? "static" : "instance";
                    }
                }

                //Add list of methods and their class value 
                $methodList[$class] = (object) $list;
            }

            //Add all the methods to the main array
            $this->initialize["methods"] = (object) $methodList;
           
            //attepmt to intantiate
            foreach($this->initialize["methods"] as $key => $methodProperty)
            {
                /**
                 * Only if the class contains methods with instance we initialize that class otherwise we 
                 * skip that class for performance since instantiation is not needed. It is indeed recommented
                 * to create static methods  classes serparated from instance methods classes.
                 */
                if(in_array("instance", (array) $methodProperty))
                {
                    #Instantiate classes with methods that contains instances only
                    $this->initialize[$key] =  new $this->initialize[$key];
                }
            }

            //Unset methods from the array.
            unset($this->initialize["methods"]);
=======
    use \Exception;
    use \ReflectionClass;
    use \ReflectionParameter;
    use Src\Mapper;

    class DI 
    {
        
        public function inject(string $class) : mixed
        {

            if($class == "string") return "";

            $dependencies = [];

            $ref = new ReflectionClass($class);         
            
            if($ref->isInterface())
            {
                if(!array_key_exists($ref->name, Mapper::$map))
                {
                    throw new Exception(
                        sprintf("Interface class not mapped: %s", $ref->name));
                }

                $ref = new ReflectionClass(Mapper::$map[$ref->name]);
            }

            $constructor = $ref->getConstructor();
            
            #stop here since there are no dependencies
            if(is_null($constructor))
            {
                return $ref->newInstance();
            }
            
            $parameters = $constructor->getParameters();

            $dependencies = $this->getDependencies($parameters);

            return $ref->newInstanceArgs($dependencies);
        }
        
        
        public function getDependencies(array $parameters) : array
        {
            $dependencies = [];
            
            foreach($parameters as $parameter)
            {
                $dependency = $parameter->getType()->getName();
                
                if(is_null($dependency))
                {
                    $dependencies[] = $this->regularParams($parameter);
                }
                else
                {
                    $dependencies[] = $this->inject($dependency);
                }
            }
            
            return $dependencies;
        }
        
        
        public function regularParams(ReflectionParameter $parameter) : mixed
        {
            if($parameter->isDefaultValueAvailable())
            {
                return $parameter->getDefaultValue();
            }
            
            throw new Exception("Unable to to get parameters");
>>>>>>> rebuildtest
        }
    }