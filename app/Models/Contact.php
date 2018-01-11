<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    /**
     * The contact table
     *
     * @var string
     */
    protected $table = 'contact';

    /**
     * Dashboard columns
     *
     * @var array
     */
    public static $dashboard_columns = [
        [ 'Date', 'created_at' ],
        [ 'Name', 'name' ],
        [ 'Email', 'email' ],
        [ 'Message', 'message' ]
    ];

    /**
     * Returns the list of all contact submissions
     *
     * @return array
     */
    public static function getContactSubmissions()
    {
        return self::orderBy('created_at', 'desc')->get();
    }
}
