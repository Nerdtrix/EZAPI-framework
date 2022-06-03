<?php
    namespace Core;
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
        }
    }
?>