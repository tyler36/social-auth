<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for Socialite Providers. To add a new provider
    |   - Add the 'provider' key to 'providers' with a TRUE / FALSE value. This is master switch for the service.
    |   - Add a 'provider' key with the following sub-keys: ['client_id', 'client_secret', 'redirect']
    |     For security purposes, each sub-key should point use ENV variables
    |     Eg. 'client_id' => env('GITHUB_CLIENT_ID')
    |
    */

    /**
     * This is the table that will store the user login information
     */
    'user_table'   => 'users',
    'user_columns' => ['auth_provider', 'auth_provider_id'],

    /**
     * This is a master switch array for providers. Provider names must set to true here to be available
     */
    'providers' => [
        'github' => true,
    ],

    /**
     * This is a working example of provider. It requires all three fields be set.
     * For security, set the variables in your ENV file
     */
    'github' => [
        'client_id'     => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect'      => env('GITHUB_CALLBACK_URL'),
    ],
];
