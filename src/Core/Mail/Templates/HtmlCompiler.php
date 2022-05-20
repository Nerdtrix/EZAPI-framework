<?php
    namespace Core\Mail\Templates;
    use SplFileObject;
    use Exception;

    class HtmlCompiler
    {

        /**
         * @method run
         * @param array parameters
         * @param string filePath
         * @return string html content
         * @throws exceptions 
         * @comment: Use this function to compile html pages with dynamic variables.
         * @How to use: Add @@variable@@ to your html anywhere you need it.
         * @note: Do not add anyspaces to the variables to prevent errors.
         */
        public static function run(array $parameters, string $templateName = "Default.html") : string
        {
            #Validate the file location
            $template = sprintf("%s%s%s", dirname(__FILE__), SLASH, $templateName);

            if(!file_exists($template)) throw new Exception("The file location does not exists: {$template}");

            $compiledHtml = null;

            #Open html file
            $file = new SplFileObject($template);

            while (!$file->eof())
            {
                #read line
                $line = $file->fgets();

                #Search for double symbools
                if (preg_match('#@@#', $line)) 
                {
                    #Get everything before the two @@ symbols
                    preg_match("/(@@)(.*?)(@@)/", $line, $parameter);

                    #Get the 3rd key since its the one without the @@
                    $parameter = $parameter[2];

                    #Validate key existance
                    if(!array_key_exists($parameter, $parameters)) throw new Exception(sprintf("Parameter %s not found in the array.", $parameter));

                    #Replace everything between the two @@ symbols
                    $line = preg_replace("/(@@)(.*?)(@@)/", $parameters[$parameter], $line);
                }

                #Append to the string
                $compiledHtml .=  $line;
            }

            #Return value
            return $compiledHtml;
        }
    }