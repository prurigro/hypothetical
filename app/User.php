<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Hash;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];

    /**
     * Update the user's password
     *
     * @var string
     */
    public function updatePassword($oldpass, $newpass)
    {
        if (Hash::check($oldpass, $this->password)) {
            $this->password = Hash::make($newpass);
            $this->save();
            return true;
        }

        return false;
    }
}
