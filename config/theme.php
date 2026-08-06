<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Theme Configuration
    |--------------------------------------------------------------------------
    |
    | Default theme settings for DattaAble template
    |
    */

    'dark_layout' => env('THEME_DARK_LAYOUT', 'light'), // 'light' or 'dark'
    'sidebar_theme' => env('THEME_SIDEBAR', 'dark'), // 'dark', 'light', or color classes
    'box_container' => env('THEME_BOX_CONTAINER', 'false'), // 'true' or 'false'
    'caption_show' => env('THEME_CAPTION', 'true'), // 'true' or 'false'
    'rtl_layout' => env('THEME_RTL', 'false'), // 'true' or 'false'
    'preset_theme' => env('THEME_PRESET', 'preset-1'), // preset-1 to preset-8
    'theme_layout' => env('THEME_LAYOUT', 'vertical'), // 'vertical' or 'horizontal'
];
