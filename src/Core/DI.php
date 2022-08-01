<?php
    namespace Core;
    use \Exception;
    use \ReflectionClass;
    use \ReflectionParameter;
    use Src\Mapper;

    class DI 
    {
        #reusable instances [class => instance]
        public static array $instances = [];
        
        
        public function inject(string $class, string $targetMethod = "") : mixed
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

            #inject class attibutes
            foreach ($ref->getAttributes() as $attr) 
            {
                $attr->newInstance();
            }

            #Execute method attibutes
            foreach ($ref->getMethods() as $method) 
            {
                if($method->name == $targetMethod)
                {
                    $attributes = $method->getAttributes();

                    foreach($attributes as $attibute)
                    {
                        #Unnused for now
                        $attibuteArgs = $attibute->getArguments();
                        
                        $attibute->newInstance();
                    }
                }
            }

            $constructor = $ref->getConstructor();

            #stop here since there are no dependencies
            if(is_null($constructor))
            {
                $newInstance = $ref->newInstance();

                #save instance
                array_push(self::$instances, [$class => $newInstance]);

                return $newInstance;
            }
            
            $parameters = $constructor->getParameters();

            $dependencies = $this->getDependencies($parameters);

            $newInstance = $ref->newInstanceArgs($dependencies);

            #save instance
            array_push(self::$instances, [$class => $newInstance]);

            return $newInstance;
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
        }




    }
?>