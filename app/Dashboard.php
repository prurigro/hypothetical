<?php

namespace App;

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
     * Authors (Credits Page)
     *
     * @return array
     */
    public static $author_credits = [
        [ 'name' => 'Kevin MacMartin', 'url' => 'https://github.com/prurigro' ]
    ];

    /**
     * Libraries (Credits Page)
     *
     * @return array
     */
    public static $library_credits = [
        [ 'name' => 'Bootstrap', 'url' => 'https://getbootstrap.com' ],
        [ 'name' => 'Font Awesome', 'url' => 'https://fontawesome.com' ],
        [ 'name' => 'GreenSock', 'url' => 'https://greensock.com/gsap' ],
        [ 'name' => 'jQuery', 'url' => 'https://jquery.org' ],
        [ 'name' => 'List.js', 'url' => 'http://listjs.com' ],
        [ 'name' => 'pickadate.js', 'url' => 'http://amsul.ca/pickadate.js/' ],
        [ 'name' => 'Popper.js', 'url' => 'https://popper.js.org' ],
        [ 'name' => 'SimpleMDE Markdown Editor', 'url' => 'https://simplemde.com' ],
        [ 'name' => 'Sortable', 'url' => 'https://github.com/RubaXa/Sortable' ],
        [ 'name' => 'SpinKit', 'url' => 'http://tobiasahlin.com/spinkit/' ],
        [ 'name' => 'Vue.js', 'url' => 'https://vuejs.org' ],
        [ 'name' => 'what-input', 'url' => 'https://github.com/ten1seven/what-input' ]
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
