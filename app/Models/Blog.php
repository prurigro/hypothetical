<?php

namespace App\Models;

use Parsedown;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Blog extends DashboardModel
{
    protected $table = 'blog';

    public static $dashboard_type = 'edit';

    public static $dashboard_help_text = '<strong>NOTE</strong>: Tags should be separated by semicolons';

    public static $dashboard_display = [ 'title', 'created_at' ];

    public static $dashboard_columns = [
        [ 'name' => 'user_id', 'type' => 'user' ],
        [ 'name' => 'created_at', 'title' => 'Date', 'type' => 'display' ],
        [ 'name' => 'title',  'type' => 'text' ],
        [ 'name' => 'body',  'type' => 'mkd' ],
        [ 'name' => 'tags', 'type' => 'text' ],
        [ 'name' => 'header-image', 'title' => 'Header Image', 'type' => 'image', 'delete' => true ]
    ];

    public static function getBlogEntries()
    {
        $blog_entries = [];

        foreach (self::orderBy(self::$dashboard_sort_column, self::$dashboard_sort_direction)->get() as $blog_entry) {
            // Add the name of the user that created the post
            $blog_entry['username'] = User::find($blog_entry->user_id)->name;

            // Add a string with the date and time the post was made
            $blog_entry['date'] = date('M j, Y @ g:iA', strtotime($blog_entry->created_at));

            // Convert the markdown in the body to html
            $blog_entry['body'] = Parsedown::instance()->setBreaksEnabled(true)->setMarkupEscaped(true)->parse($blog_entry['body']);

            // Replace the tags string with an array
            $tags = [];

            foreach (explode(';', $blog_entry['tags']) as $tag) {
                array_push($tags, $tag);
            }

            $blog_entry['tags'] = $tags;

            // Add the header image if one exists
            $header_image_path = '/uploads/blog/img/' . $blog_entry->id . '-header-image.jpg';
            $blog_entry['headerimage'] = file_exists(base_path() . '/public' . $header_image_path) ? $header_image_path : '';

            // Add the processed blog entry to the array
            array_push($blog_entries, $blog_entry);
        }

        return $blog_entries;
    }
}
