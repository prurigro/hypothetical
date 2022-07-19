<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use File;
use Image;
use App\Traits\Timestamp;

class DashboardModel extends Model
{
    use Timestamp;

    /*
     * The dashboard page type
     *
     * @var array
     */
    public static $dashboard_type = 'view';

    /*
     * Dashboard heading
     *
     * @var string
     */
     public static $dashboard_heading = null;

    /*
     * Whether the model can be exported
     *
     * @var boolean
     */
    public static $export = false;

    /*
     * Whether new rows can be created
     *
     * @var boolean
     */
    public static $create = true;

    /*
     * Whether new rows can be deleted
     *
     * @var boolean
     */
    public static $delete = true;

    /*
     * Whether rows can be filtered
     *
     * @var boolean
     */
    public static $filter = true;

    /*
     * Number of items per page (0 for unlimited)
     *
     * @var number
     */
    public static $items_per_page = 0;

    /*
     * Query parameters to remember
     *
     * @var number
     */
    public static $valid_query_params = [];

    /*
     * Dashboard help text
     *
     * @var string
     */
    public static $dashboard_help_text = '';

    /*
     * Array of columns to display in the dashboard edit list
     *
     * @var array
     */
    public static $dashboard_display = [];

    /**
     * Whether to allow click-and-drag reordering
     *
     * @var boolean
     */
    public static $dashboard_reorder = false;

    /**
     * The dashboard sort column
     *
     * @var array
     */
    public static $dashboard_sort_column = 'created_at';

    /**
     * The dashboard sort direction (only when $dashboard_reorder == false)
     *
     * @var array
     */
    public static $dashboard_sort_direction = 'desc';

    /**
     * The dashboard button
     *
     * @var array
     */
    public static $dashboard_button = [];

    /**
     * The dashboard id link
     *
     * @var array
     */
    public static $dashboard_id_link = [];

    /**
     * The default image extension when none is set
     *
     * @var string
     */
    public static $default_image_ext = 'jpg';

    /**
     * Functionality to run when various events occur
     *
     * @return null
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function($item) {
            // delete associated images and files if they exist
            foreach ($item::$dashboard_columns as $column) {
                if ($column['type'] == 'image') {
                    $item->deleteImage($column['name'], false);
                } else if ($column['type'] == 'file') {
                    $item->deleteFile($column['name'], false);
                }
            }
        });
    }

    /**
     * Returns the dashboard heading
     *
     * @return string
     */
    public function getDashboardHeading()
    {
        return static::$dashboard_heading == null ? ucfirst($this->getTable()) : static::$dashboard_heading;
    }

    /**
     * Return the upload path for a given type
     *
     * @return string
     */
    public function getUploadsPath($type)
    {
        if ($type == 'image') {
            return '/uploads/' . $this->getTable() . '/img/';
        } else if ($type == 'file') {
            return '/uploads/' . $this->getTable() . '/files/';
        }
    }

    /**
     * Save an image
     *
     * @return string
     */
    public function saveImage($name, $file)
    {
        // Fail if the user doesn't have permission
        if (!$this->userCheck()) {
            return 'permission-fail';
        }

        $max_width = 0;
        $max_height = 0;

        // Retrieve the column
        $column = static::getColumn($name);

        // Return an error if no column is found
        if ($column == null) {
            return 'no-such-column-fail';
        }

        // Use the configured image extension or fall back on the default if none is set
        if (array_key_exists('ext', $column)) {
            $main_ext = $column['ext'];
        } else {
            $main_ext = $this::$default_image_ext;
        }

        // Create the directory if it doesn't exist
        $directory = public_path($this->getUploadsPath('image'));
        File::makeDirectory($directory, 0755, true, true);

        // Set the base file path (including the file name but not the extension)
        $base_filename = $directory . $this->id . '-' . $name . '.';

        if ($main_ext == 'svg') {
            // Save the image provided it's an SVG
            if (gettype($file) == 'string') {
                if (!preg_match('/\.svg$/i', $file)) {
                    return 'incorrect-format-fail';
                }

                copy($file, $base_filename . $main_ext);
            } else {
                if ($file->extension() != 'svg') {
                    return 'incorrect-format-fail';
                }

                $file->move($directory, $base_filename . $main_ext);
            }
        } else {
            // Update the maximum width if it's been configured
            if (array_key_exists('max_width', $column)) {
                $max_width = $column['max_width'];
            }

            // Update the maximum height if it's been configured
            if (array_key_exists('max_height', $column)) {
                $max_height = $column['max_height'];
            }

            $image = Image::make($file);

            if ($max_width > 0 || $max_height > 0) {
                $width = $image->width();
                $height = $image->height();
                $new_width = null;
                $new_height = null;

                if ($max_width > 0 && $max_height > 0) {
                    if ($width > $max_width || $height > $max_height) {
                        $new_width = $max_width;
                        $new_height = ($new_width / $width) * $height;

                        if ($new_height > $max_height) {
                            $new_width = ($max_height / $height) * $width;
                        }
                    }
                } else if ($max_width > 0) {
                    if ($width > $max_width) {
                        $new_width = $max_width;
                    }
                } else if ($height > $max_height) {
                    $new_height = $max_height;
                }

                if (!is_null($new_width) || !is_null($new_height)) {
                    $image->resize($new_width, $new_height, function($constraint) {
                        $constraint->aspectRatio();
                    });
                }
            }

            $image->save($base_filename . $main_ext);
            $image->save($base_filename . 'webp');
        }

        return 'success';
    }

    /*
     * Delete an image
     *
     * @return string
     */
    public function deleteImage($name, $not_exist_fail)
    {
        // Fail if the user doesn't have permission
        if (!$this->userCheck()) {
            return 'permission-fail';
        }

        // Set up our variables
        $extensions = [];

        // Retrieve the column
        $column = static::getColumn($name);

        // Return an error if no column is found
        if ($column == null) {
            return 'no-such-column-fail';
        }

        // Use the configured image extension or fall back on the default if none is set
        if (array_key_exists('ext', $column)) {
            $main_ext = $column['ext'];
        } else {
            $main_ext = $this::$default_image_ext;
        }

        // Build the set of extensions to delete
        array_push($extensions, $main_ext);

        // If the image extension isn't svg also delete the webp
        if ($main_ext != 'svg') {
            array_push($extensions, 'webp');
        }

        // Delete each image
        foreach ($extensions as $ext) {
            // Get the full path of the image
            $image = public_path($this->getUploadsPath('image') . $this->id . '-' . $name . '.' . $ext);

            // Try to delete the image
            if (file_exists($image)) {
                if (!unlink($image)) {
                    return 'image-delete-fail';
                }
            } else if ($not_exist_fail) {
                return 'image-not-exists-fail';
            }
        }

        // Success
        return 'success';
    }

    /**
     * Save a file
     *
     * @return string
     */
    public function saveFile($name, $file)
    {
        // Fail if the user doesn't have permission
        if (!$this->userCheck()) {
            return 'permission-fail';
        }

        // Retrieve the column
        $column = static::getColumn($name);

        // Return an error if no column is found
        if ($column == null) {
            return 'no-such-column-fail';
        }

        // Fail if an ext hasn't been declared
        if (!array_key_exists('ext', $column)) {
            return 'no-configured-extension-fail';
        }

        // Store the extension
        $ext = $column['ext'];

        // Create the directory if it doesn't exist
        $directory = public_path($this->getUploadsPath('file'));
        File::makeDirectory($directory, 0755, true, true);

        // Set the base file path (including the file name but not the extension)
        $base_filename = $directory . $this->id . '-' . $name . '.';

        // Save the file provided it's the correct extension
        if (gettype($file) == 'string') {
            if (!preg_match("/\.$ext/i", $file)) {
                return 'incorrect-format-fail';
            }

            copy($file, $base_filename . $ext);
        } else {
            if ($file->extension() != $ext) {
                return 'incorrect-format-fail';
            }

            $file->move($directory, $this->id . '-' . $name . '.' . $ext);
        }

        // Success
        return 'success';
    }

    /*
     * Delete a file
     *
     * @return string
     */
    public function deleteFile($name, $not_exist_fail)
    {
        // Fail if the user doesn't have permission
        if (!$this->userCheck()) {
            return 'permission-fail';
        }

        // Retrieve the column
        $column = static::getColumn($name);

        // Return an error if no column is found
        if ($column == null) {
            return 'no-such-column-fail';
        }

        // Fail if an ext hasn't been declared
        if (!array_key_exists('ext', $column)) {
            return 'no-configured-extension-fail';
        }

        // Store the extension
        $ext = $column['ext'];

        // Get the full path of the file
        $file = public_path($this->getUploadsPath('file') . $this->id . '-' . $name . '.' . $ext);

        // Try to delete the file
        if (file_exists($file)) {
            if (!unlink($file)) {
                return 'file-delete-fail';
            }
        } else if ($not_exist_fail) {
            return 'file-not-exists-fail';
        }

        // Success
        return 'success';
    }

    /**
     * Determine whether a user column exists and whether it matches the current user if it does
     *
     * @return boolean
     */
    public function userCheck()
    {
        $user_check = true;

        foreach (static::$dashboard_columns as $column) {
            if (array_key_exists('type', $column) && $column['type'] == 'user') {
                if ($this->{$column['name']} != Auth::id()) {
                    $user_check = false;
                }

                break;
            }
        }

        return $user_check;
    }

    /**
     * Get the file extension for an image
     *
     * @return string|null
     */
    public static function getColumn($name)
    {
        foreach (static::$dashboard_columns as $column) {
            if ($column['name'] == $name) {
                return $column;
            }
        }

        return null;
    }

    /**
     * Return an array of column 'headings' or 'names'
     *
     * @return array
     */
    public static function getDashboardColumnData($type, $all_columns = true)
    {
        $column_data = [];

        foreach (static::$dashboard_columns as $column) {
            if ($all_columns || !array_key_exists('type', $column) || !preg_match('/^(hidden|user|image|file)$/', $column['type'])) {
                if ($type == 'headings') {
                    if (array_key_exists('title', $column)) {
                        array_push($column_data, $column['title']);
                    } else {
                        array_push($column_data, ucfirst($column['name']));
                    }
                } else if ($type == 'names') {
                    array_push($column_data, $column['name']);
                }
            }
        }

        return $column_data;
    }

    /**
     * Perform a search against the columns in $dashboard_display
     *
     * @return array
     */
    public static function searchDisplay($term, $query = null)
    {
        if (static::$filter) {
            $first = true;

            if ($query === null) {
                $query = self::orderBy(static::$dashboard_sort_column, static::$dashboard_sort_direction);
            }

            foreach (static::$dashboard_display as $display) {
                $type = '';

                foreach (static::$dashboard_columns as $column) {
                    if ($column['name'] === $display) {
                        $type = $column['type'];
                    }
                }

                if ($type !== '' && $type !== 'image') {
                    if ($first) {
                        $query->where($display, 'LIKE', '%' . $term . '%');
                    } else {
                        $query->orWhere($display, 'LIKE', '%' . $term . '%');
                    }

                    $first = false;
                }
            }

            return $query;
        } else {
            return [];
        }
    }

    /**
     * Return data for the dashboard
     *
     * @return array
     */
    public static function getDashboardData($include_param_display = false)
    {
        $sort_direction = static::$dashboard_reorder ? 'desc' : static::$dashboard_sort_direction;
        $query = self::orderBy(static::$dashboard_sort_column, $sort_direction);
        $query_param_display = [];

        foreach (static::$dashboard_columns as $column) {
            if (array_key_exists('type', $column) && $column['type'] == 'user') {
                $query->where($column['name'], Auth::id());
                break;
            }
        }

        if (count(static::$valid_query_params) > 0) {
            foreach (static::$valid_query_params as $param) {
                if (request()->query($param['key'], null) != null) {
                    if ($include_param_display) {
                        $query_param_model = 'App\\Models\\' . $param['model'];
                        $query_param_column = $query_param_model::find(request()->query($param['key']));

                        if ($query_param_column !== null) {
                            array_push($query_param_display, [
                                'title' => $param['title'],
                                'value' => $query_param_column[$param['display']]
                            ]);
                        }
                    }

                    $query->where($param['column'], request()->query($param['key']));
                }
            }
        }

        if (static::$items_per_page === 0) {
            $results = $query->get();
        } else {
            if (static::$filter && request()->query('search', null) != null) {
                $query = static::searchDisplay(request()->query('search'), $query);
            }

            $results = $query->paginate(static::$items_per_page);
        }

        if ($include_param_display) {
            return [
                'rows' => $results,
                'paramdisplay' => $query_param_display
            ];
        } else {
            return $results;
        }
    }

    /**
     * Retrieve the current query string containing valid query parameters
     *
     * @return string
     */
    public static function getQueryString()
    {
        $valid_query_params = static::$valid_query_params;
        $string = '';

        if (static::$items_per_page !== 0 && static::$filter) {
            array_push($valid_query_params, [ 'key' => 'search' ]);
        }

        foreach ($valid_query_params as $param) {
            if (request()->query($param['key'], null) != null) {
                if ($string !== '') {
                    $string .= '&';
                }

                $string .= $param['key'] . '=' . request()->query($param['key']);
            }
        }

        return $string;
    }
}
