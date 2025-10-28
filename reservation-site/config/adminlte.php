<?php

return [
    // ... (previous config)

    'menu' => [
        // Navbar items:
        [
            'type' => 'navbar-search',
            'text' => 'search',
            'topnav_right' => true,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
            'text' => 'Dashboard',
            'url' => 'dashboard',
            'icon' => 'fas fa-fw fa-tachometer-alt',
        ],
        [
            'text' => 'My Reservations',
            'url' => 'reservations',
            'icon' => 'fas fa-fw fa-calendar-alt',
        ],
        [
            'text' => 'My Calendar',
            'route' => 'expert.calendar',
            'icon' => 'fas fa-fw fa-calendar',
        ],
        [
            'header' => 'ADMINISTRATION',
        ],
        [
            'text' => 'Manage Reservations',
            'route' => 'admin.reservations.index',
            'icon' => 'fas fa-fw fa-cogs',
        ],
    ],

    // ... (rest of the config)
];
