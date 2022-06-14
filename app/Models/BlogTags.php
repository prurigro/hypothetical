<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogTags extends DashboardModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'blog_tags';

    public static $dashboard_type = 'list';

    public static $dashboard_columns = [
        [ 'type' => 'string', 'name' => 'name' ]
    ];
}
