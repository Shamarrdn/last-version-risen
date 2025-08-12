<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Popup Authentication Credentials
    |--------------------------------------------------------------------------
    |
    | These credentials are used for the admin popup authentication which
    | provides an additional layer of security for admin pages.
    |
    */
    'popup_username' => env('ADMIN_POPUP_USERNAME', 'admin'),
    'popup_password' => env('ADMIN_POPUP_PASSWORD', 'admin123'),
];
