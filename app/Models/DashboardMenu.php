<?php

namespace App\Models;

class DashboardMenu
{
    /**
     * Dashboard Menu
     *
     * @return array
     */
    public static $menu = [
        [
            'title' => 'Submissions',

            'submenu' => [
                [
                    'title' => 'Contact',
                    'type' => 'view',
                    'model' => 'contact'
                ],
                [
                    'title' => 'Subscriptions',
                    'type' => 'view',
                    'model' => 'subscriptions'
                ]
            ]
        ]
    ];
}
