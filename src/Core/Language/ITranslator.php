<?php
    namespace Core\Language;

    interface ITranslator
    {
        function translate(string $key) : string;

        function setLocale(string $locale): void;

        function getLocale() : string;
    }
?>