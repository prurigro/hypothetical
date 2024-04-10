<?php

namespace App\Models;

class Meta extends DashboardModel
{
    protected $table = 'meta';

    public static $create = false;

    public static $items_per_page = 0;

    public static $dashboard_help_text = 'The path must start with a forward slash (eg: "/" or "/pagename")';

    public static $dashboard_type = 'edit';

    public static $dashboard_display = [ 'title', 'path' ];

    public static $dashboard_columns = [
        [ 'name' => 'path', 'required' => true, 'unique' => true, 'type' => 'string' ],
        [ 'name' => 'title', 'required' => true, 'unique' => false, 'type' => 'string' ],
        [ 'name' => 'description', 'required' => true, 'unique' => false, 'type' => 'text' ],
        [ 'name' => 'keywords', 'required' => true, 'unique' => false, 'type' => 'string' ]
    ];

    public static function getData($path)
    {
        if (!preg_match('/^\//', $path)) {
            $path = "/$path";
        }

        if (preg_match('/^\/(dashboard|login|register)/', $path)) {
            $page = [
                'title' => 'Dashboard' . ' | ' . env('APP_NAME'),
                'description' => '',
                'keywords' => ''
            ];
        } else {
            $page = self::select('title', 'description', 'keywords')->where('path', "$path")->first();

            if ($page == null) {
                $page = [
                    'title' => 'Page Not Found' . ' | ' . env('APP_NAME'),
                    'description' => 'The requested page cannot be found',
                    'keywords' => ''
                ];
            }
        }

        return $page;
    }
}
