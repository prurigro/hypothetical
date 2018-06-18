<?php

namespace App\Models;

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
