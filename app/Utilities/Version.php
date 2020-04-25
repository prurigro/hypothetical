<?php

namespace App\Utilities;

class Version
{
    /**
     * The version file
     *
     * @var string
     */
    private static $version_file_path = '/storage/app/__version.txt';

    /**
     * Returns the current version (or 0 if none is set)
     *
     * @return string
     */
    public static function get()
    {
        $full_version_file_path = base_path() . self::$version_file_path;
        $version = '0';

        if (file_exists($full_version_file_path)) {
            $version = trim(file_get_contents($full_version_file_path));
        }

        return $version;
    }
}
