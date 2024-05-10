<?php

namespace App\Utilities;

class Language
{
    /**
     * The language cookie name
     *
     * @var string
     */
    public static $language_cookie = 'locale';

    /**
     * Retrieve the language from the cookie or fall back on the default
     *
     * @return string
     */
    public static function getSessionLanguage()
    {
        return session(self::$language_cookie, env('APP_LOCALE', 'en'));
    }

    /**
     * Set the configured language cookie
     *
     * @param string
     * @return boolean
     */
    public static function setSessionLanguage($language)
    {
        session([ self::$language_cookie => $language ]);
        return self::getSessionLanguage() == $language;
    }

    /**
     * Take an array of strings and return the string associated with
     * the currently configured language or fall back on the default
     *
     * @param array
     * @return string
     */
    public static function select($string_array)
    {
        $session_language = self::getSessionLanguage();
        $default_language = env('APP_LOCALE');
        $string = '';

        if (array_key_exists($session_language, $string_array)) {
            $string = $string_array[$session_language];
        } else if (array_key_exists($default_language, $string_array)) {
            $string = $string_array[$default_language];
        }

        return $string;
    }
}
