<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriptions extends DashboardModel
{
    protected $table = 'subscriptions';

    public static $export = true;

    public static $dashboard_columns = [
        [ 'title' => 'Date', 'name' => 'created_at' ],
        [ 'name' => 'email' ],
        [ 'name' => 'name' ]
    ];
}
