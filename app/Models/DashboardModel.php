<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
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
     * Performs a search against the columns in $dashboard_display
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
     * Returns data for the dashboard
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
     * Retrieves the current query string containing valid query parameters
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
