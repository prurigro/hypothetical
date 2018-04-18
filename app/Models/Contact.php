<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends DashboardModel
{
    protected $table = 'contact';

    public static $dashboard_heading = 'Contact Form Submissions';

    public static $export = true;

    public static $dashboard_columns = [
        [ 'name' => 'created_at', 'title' => 'Date' ],
        [ 'name' => 'name' ],
        [ 'name' => 'email' ],
        [ 'name' => 'message' ]
    ];
}
