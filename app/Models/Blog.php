<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends DashboardModel
{
    protected $table = 'blog';

    public static $dashboard_type = 'edit';

    public static $dashboard_help_text = '<strong>NOTE</strong>: Tags should be separated by semicolons';

    public static $dashboard_display = [ 'title', 'created_at' ];

    public static $dashboard_columns = [
        [ 'name' => 'user_id', 'type' => 'user' ],
        [ 'name' => 'created_at', 'title' => 'Date', 'type' => 'display' ],
        [ 'name' => 'title',  'type' => 'text' ],
        [ 'name' => 'body',  'type' => 'mkd' ],
        [ 'name' => 'tags', 'type' => 'text' ],
        [ 'name' => 'header-image', 'title' => 'Header Image', 'type' => 'image', 'delete' => true ]
    ];
}
