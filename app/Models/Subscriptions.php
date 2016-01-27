<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    // the subscriptions table
    protected $table = 'subscriptions';

    // returns the list of all subscriptions
    public static function getSubscriptions()
    {
        return self::orderBy('created_at', 'desc')->get();
    }
}
