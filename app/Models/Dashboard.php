<?php

namespace App\Models;

class Dashboard
{
    /**
     * Dashboard Menu
     *
     * @return array
     */
    public static $menu = [
        [
            'title' => 'Blog',
            'type'  => 'edit',
            'model' => 'blog'
        ],

        [
            'title' => 'Form Submissions',

            'submenu' => [
                [
                    'title' => 'Contact',
                    'type'  => 'view',
                    'model' => 'contact'
                ],
                [
                    'title' => 'Subscriptions',
                    'type'  => 'view',
                    'model' => 'subscriptions'
                ]
            ]
        ]
    ];

    /**
     * Retrieve a Dashboard Model
     *
     * @return model
     */
    public static function getModel($model, $type = null)
    {
        $model_name = ucfirst($model);

        if (file_exists(app_path() . '/Models/' . $model_name . '.php')) {
            $model_class = 'App\\Models\\' . ucfirst($model);

            if ($type != null && $type != $model_class::$dashboard_type) {
                return null;
            }

            return $model_class;
        } else {
            return null;
        }
    }
}
