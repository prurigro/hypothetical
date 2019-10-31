<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Hash;
use App\Traits\Timestamp;

class User extends Authenticatable
{
    use Notifiable;
    use Timestamp;

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The default user profile image
     *
     * @var string
     */
    public static $default_profile_image = '/img/dashboard/missing-profile.png';

    /**
     * The directory user profile uploads are stored in
     *
     * @var string
     */
    public static $profile_image_dir = '/uploads/user/img/';

    /**
     * The maximum profile image width and height
     *
     * @var array
     */
    public static $profile_image_max = [
        'width' => 512,
        'height' => 512
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

    /**
     * Get user profile image
     *
     * @var string
     */
    public function profileImage($show_full_path = false, $always_return_path = false)
    {
        $site_path = self::$profile_image_dir . $this->id . '-profile.png';
        $file_path = base_path() . '/public' . $site_path;

        if (file_exists($file_path) || $always_return_path) {
            return $show_full_path ? $file_path : $site_path;
        } else {
            return null;
        }
    }
}
