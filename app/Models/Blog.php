<?php

namespace App\Models;

use Parsedown;
use App\Models\User;
use App\Models\BlogTags;

class Blog extends DashboardModel
{
    protected $table = 'blog';

    public static $items_per_page = 10;

    public static $dashboard_type = 'edit';

    public static $dashboard_display = [ 'title', 'created_at' ];

    public static $dashboard_columns = [
        [ 'name' => 'user_id', 'type' => 'user' ],
        [ 'name' => 'created_at', 'title' => 'Date', 'type' => 'display' ],
        [ 'name' => 'title', 'required' => true, 'unique' => true, 'type' => 'string' ],
        [ 'name' => 'body', 'required' => true,  'type' => 'mkd' ],
        [ 'name' => 'header-image', 'title' => 'Header Image', 'type' => 'image', 'delete' => true, 'ext' => 'jpg' ],
        [ 'name' => 'tags', 'type' => 'list', 'model' => 'BlogTags', 'foreign' => 'blog_id', 'sort' => 'order' ]
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

            foreach (BlogTags::where('blog_id', $blog_entry->id)->orderBy('order')->get() as $tag) {
                array_push($tags, $tag['name']);
            }

            $blog_entry['tags'] = $tags;

            // Add the header image if one exists
            $header_image_path = $blog_entry->getUploadsPath('image') . $blog_entry->id . '-header-image.jpg';
            $blog_entry['headerimage'] = file_exists(public_path($header_image_path)) ? $header_image_path . '?version=' . $blog_entry->timestamp() : '';

            // Add the processed blog entry to the array
            array_push($blog_entries, $blog_entry);
        }

        return $blog_entries;
    }
}
