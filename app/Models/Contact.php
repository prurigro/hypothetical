<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model {

    // The contact table
    protected $table = 'contact';

    // Returns the list of all contact submissions
    public static function getContactSubmissions()
    {
        return self::orderBy('created_at', 'desc')->get();
    }

}
