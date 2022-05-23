<?php
    namespace Core\Language;

use Exception;

    class Translator implements ITranslator
    {
        private array $m_dictionary;
        

        public const LANGUAGES = [
            "en_US" => [
                "name" => "English (United States)", 
                "locale" => "en_US",
                "charset" => "UTF-8"
            ],
            "es_US" => [
                "name" => "Spanish (United States)", 
                "locale" => "es_US",
                "charset" => "UTF-8"
            ]
        ];

        private const DICTIONARY_PATHS = [
            "en_US" => "en/en_US.php",
            "es_US" => "es/es_US.php"
        ];

        

        public function __construct()
        {
            $this->loadDictionary(EZENV["DEFAULT_LOCALE"]);
        }


        /**
         * @param string key
         * @return string
         */
        public function translate(string $key) : string
        {
            if(array_key_exists($key, $this->m_dictionary))
            {
                return $this->m_dictionary[$key];
            }

            #Invalid key
            return $key; 
        }


        /**
         * @param string locale
         */
        public function setLocale(string $locale): void
        {
            if(!array_key_exists($locale, self::LANGUAGES))
            {
               throw new Exception("invalid language locale");
            }

            $this->loadDictionary($locale);
        }


        /**
         * @param string locale
         */
        private function loadDictionary(string $locale) : void
        {
            $dictionary = sprintf(
                "%s%sCore%sLanguage%s%s", 
                SRC_DIR, 
                SLASH, 
                SLASH, 
                SLASH, 
                self::DICTIONARY_PATHS[$locale]
            );


            if(!file_exists($dictionary))
            {
                throw new Exception ("Invalid language path");
            }


            #Load the new language
            $this->m_dictionary = require($dictionary);
        }
    }