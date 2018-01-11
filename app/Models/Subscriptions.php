<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    /**
     * The subscriptions table
     *
     * @var string
     */
    protected $table = 'subscriptions';

    /**
     * Dashboard columns
     *
     * @var array
     */
    public static $dashboard_columns = [
        [ 'Date', 'created_at' ],
        [ 'Email', 'email' ],
        [ 'Name', 'name' ]
    ];

    /**
     * Returns the list of all subscriptions
     *
     * @return array
     */
    public static function getSubscriptions()
    {
        return self::orderBy('created_at', 'desc')->get();
    }
}
