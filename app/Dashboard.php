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
        [ 'name' => 'EasyMDE Markdown Editor', 'url' => 'https://github.com/Ionaru/easy-markdown-editor' ],
        [ 'name' => 'flatpickr', 'url' => 'https://flatpickr.js.org' ],
        [ 'name' => 'Font Awesome', 'url' => 'https://fontawesome.com', 'license' => 'https://fontawesome.com/license' ],
        [ 'name' => 'GreenSock', 'url' => 'https://greensock.com/gsap' ],
        [ 'name' => 'jQuery', 'url' => 'https://jquery.com' ],
        [ 'name' => 'List.js', 'url' => 'http://listjs.com' ],
        [ 'name' => 'Popper.js', 'url' => 'https://popper.js.org' ],
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

        // Ensure the model has been declared in the menu
        $model_in_menu = false;

        foreach (self::$menu as $menu_item) {
            if (array_key_exists('submenu', $menu_item)) {
                // Check each item if this is a submenu
                foreach ($menu_item['submenu'] as $submenu_item) {
                    if ($submenu_item['model'] == $model) {
                        $model_in_menu = true;
                        break;
                    }
                }
            } else {
                // Check the menu item
                if ($menu_item['model'] == $model) {
                    $model_in_menu = true;
                }
            }

            // Don't bother continuing if we've already confirmed it's in the menu
            if ($model_in_menu) {
                break;
            }
        }

        if ($model_in_menu && file_exists(app_path() . '/Models/' . $model_name . '.php')) {
            $model_class = 'App\\Models\\' . $model_name;

            if ($type != null && $type != $model_class::$dashboard_type) {
                return null;
            }

            return new $model_class;
        } else {
            return null;
        }
    }

    /**
     * Checks the registration status against the REGISTRATION variable in .env
     *
     * @return boolean
     */
    public static function canRegister()
    {
        $registration_status = env('REGISTRATION', false);
        return $registration_status === true || $registration_status === \Request::ip();
    }
}
