# Introduction
A simple package to add authentication via Socialite

## User Flow
Route: ```login.sns```
    - Validate provider
    - Send user to provider (expects callback)

Route:  ```login.sns.callback```
    - Validate provider
    - Create / Update User
    - Login if valid

## Setup
- Install package
```
composer require tyler36/social-auth
```

- Update User model to include 2 new columns. You'll also need to update any factory models.
```
    $table->string('auth_provider')->nullable();
    $table->string('auth_provider_id')->nullable();
```

- Publish assets
```
php artisan vendor:publish --provider=Tyler36\SocialAuth\SocialAuthServiceProvider
```

- Add social-auth routes to ```./routes/web.php```
```
require __DIR__.'/socialauth.php';
```

- Adding providers.
Create API key information for each provider you would like to you. Each provider requires a ```client_id```, ```client_secret```, & ```client_callback_url```.
A selection of some providers API website can be found in ```resources/lang/vendor/en/message.php``` file.

- Update the services config file
For security, it is recommended to add this to your ```ENV`` file, then add/update the section in you ```config/services.php``` file.
EG.:
```
    'github' => [
        'client_id'     => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect'      => env('GITHUB_CALLBACK_URL'),
    ],
```

- Update the package config file
You will also need to add an entry into the ```config/socialauth``` file before this package recognizes the provider as valid.
EG.:
```
 'github' => true
```

- Create entry / exit endpoints
Finally, you'll need to a link somewhere to the provider. By default, this would be 'login/{provider}', or you can use the named route helper: ```route('login.sns', {provider})```.
This package is set to return authenticated user to `/home` so add something there too.


## Testing
There are some (basic) tests.
```
phpunit .\vendor\tyler36\social-auth\tests
```
