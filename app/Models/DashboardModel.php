<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class DashboardModel extends Model
{
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
     * The dashboard buttons
     *
     * @var array
     */
    public static $dashboard_button = [];

    /**
     * Returns the dashboard heading
     *
     * @return string
     */
    public static function getDashboardHeading($model)
    {
        return static::$dashboard_heading == null ? ucfirst($model) : static::$dashboard_heading;
    }

    /**
     * Returns an array of column 'headings' or 'names'
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
     * Returns data for the dashboard
     *
     * @return array
     */
    public static function getDashboardData()
    {
        $sort_direction = static::$dashboard_reorder ? 'desc' : static::$dashboard_sort_direction;
        $query = self::orderBy(static::$dashboard_sort_column, 'desc');

        foreach (static::$dashboard_columns as $column) {
            if (array_key_exists('type', $column) && $column['type'] == 'user') {
                $query->where($column['name'], Auth::id());
                break;
            }
        }


        return $query->get();
    }

    /**
     * Determines whether a user column exists and whether it matches the current user if it does
     *
     * @return boolean
     */
    public function userCheck() {
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
}
