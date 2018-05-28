<?php

// Authenticate via Socialite
Route::get('login/{provider}', ['as' => 'login.sns', 'uses' => '\App\Http\Controllers\Auth\SocialAuthController@login']);
Route::get('login/{provider}/callback', [
    'as'   => 'login.sns.callback',
    'uses' => '\App\Http\Controllers\Auth\SocialAuthController@callback',
]);
