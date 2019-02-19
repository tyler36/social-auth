<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;
use Tests\Unit\ProvidersTest;

/**
 * Class GuestsCanLogInToSocialNetworksTest
 *
 * @test
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class GuestsCanLogInToSocialNetworksTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function guests_can_not_log_into_a_disabled_provider()
    {
        // SETUP:       Ensure config is DISABLED
        $provider = 'invalid';
        config()->set("services.socialite.${provider}", false);

        // VISIT:       Attempt login of disabled provider
        $this->withExceptionHandling()
            ->get(route('login.sns', [$provider]))
            ->assertStatus(401);

        $this->withExceptionHandling()
            ->get(route('login.sns.callback', [$provider]))
            ->assertStatus(401);
    }

    /**
     * @test
     */
    public function a_guest_is_redirected_to_social_network_on_login()
    {
        // SETUP:      Provider is configured
        $provider = ProvidersTest::setupValidProvider();

        // SETUP:       Mock Socialite response
        Socialite::shouldReceive('driver->with->redirect')
            ->andReturn(redirect('/'));

        // VISIT:       Page and see assert redirect
        $this->get(route('login.sns', $provider))
            ->assertStatus(302);
    }

    /**
     * @test
     */
    public function a_guest_can_log_in_as_a_new_user()
    {
        // SETUP:       Expected user
        list($socialUser, $user) = $this->generateSocialMock();
        $this->assertDatabaseMissing('users', [
            'auth_provider'    => $user->auth_provider,
            'auth_provider_id' => $user->auth_provider_id,
        ]);

        // VISIT:       Endpoint
        Socialite::shouldReceive('driver->user')
            ->andReturn($socialUser);

        $this->withoutExceptionHandling()
            ->get(route('login.sns.callback', $user->auth_provider));

        // ASSERT:      Member was logged in
        $this->assertTrue(auth()->check());

        // ASSERT:      Database was updated
        $this->assertDatabaseHas('users', [
            'auth_provider'    => $user->auth_provider,
            'auth_provider_id' => $user->auth_provider_id,
        ]);
    }

    /**
     * @test
     */
    public function a_guest_can_log_in_as_an_existing_user()
    {
        // SETUP:       Expected user
        $existingUser            = factory(User::class)->create();
        list($socialUser, $user) = $this->generateSocialMock($existingUser);

        $this->assertDatabaseHas('users', [
            'auth_provider'    => $existingUser->auth_provider,
            'auth_provider_id' => $existingUser->auth_provider_id,
            'name'             => $existingUser->name,
        ]);

        // VISIT:       Endpoint
        Socialite::shouldReceive('driver->user')
            ->andReturn($socialUser);

        $this->withoutExceptionHandling()
            ->get(route('login.sns.callback', $existingUser->auth_provider));

        // ASSERT:      Member was logged in
        $this->assertTrue(auth()->check());

        $this->assertDatabaseHas('users', [
            'auth_provider'    => $existingUser->auth_provider,
            'auth_provider_id' => $existingUser->auth_provider_id,
            'name'             => $existingUser->name,
        ]);
    }

    /**
     * @test
     */
    public function a_member_is_required_to_add_email_if_missing()
    {
        // SETUP:       Expected user
        $user                    = factory(User::class)->make(['email' => '']);
        list($socialUser, $user) = $this->generateSocialMock($user);
        $this->assertDatabaseMissing('users', [
            'auth_provider'    => $user->auth_provider,
            'auth_provider_id' => $user->auth_provider_id,
        ]);

        // ASSERT:      Email is missing
        $this->assertSame('', $user->email);

        // VISIT:       Endpoint
        Socialite::shouldReceive('driver->user')
            ->andReturn($socialUser);
        $this->withoutExceptionHandling()
            ->get(route('login.sns.callback', $user->auth_provider))
            ->assertRedirect(route('user.edit', 1))
            ->assertSessionHasErrors('email', trans('validation.required', ['attribute' => 'email']));

        // ASSERT:      Member was logged in
        $this->assertTrue(auth()->check());

        // ASSERT:      Database was updated
        $this->assertDatabaseHas('users', [
            'auth_provider'    => $user->auth_provider,
            'auth_provider_id' => $user->auth_provider_id,
            'email'            => null,
        ]);
    }

    /**
     * @test
     */
    public function a_member_is_required_to_change_their_name_if_it_already_exists()
    {
        // SETUP:       Expected user
        $existingUser            = factory(User::class)->create();
        $user                    = factory(User::class)->make(['name' => $existingUser->name]);
        list($socialUser, $user) = $this->generateSocialMock($user);

        $this->assertDatabaseMissing('users', [
            'auth_provider'    => $user->auth_provider,
            'auth_provider_id' => $user->auth_provider_id,
        ]);

        // ASSERT:      Name already exists
        $this->assertSame($existingUser->name, $user->name);
        $this->assertDatabaseHas('users', ['name' => $existingUser->name]);

        // VISIT:       Endpoint
        Socialite::shouldReceive('driver->user')
            ->andReturn($socialUser);
        $this->withoutExceptionHandling()
            ->get(route('login.sns.callback', $user->auth_provider))
            ->assertRedirect(route('user.edit', 2))
            ->assertSessionHasErrors('name', trans('validation.unique', ['attribute' => 'name']));

        // ASSERT:      Member was logged in
        $this->assertTrue(auth()->check());

        // ASSERT:      Database was updated
        $this->assertDatabaseHas('users', [
            'auth_provider'    => $user->auth_provider,
            'auth_provider_id' => $user->auth_provider_id,
        ]);
    }

    /**
     * HELPER:              Mock a socialite user object, based on a App\User object ($user)
     *
     * @param App\User $user
     *
     * @return array [$userSocialite, $user]
     */
    public function generateSocialMock($user = null)
    {
        $user = $user ?: factory(User::class)->make([
            'name'  => 'example user',
        ]);

        $userSocialite = (object) [
            'id'       => $user->auth_provider_id,
            'nickname' => $user->name,
            'name'     => $user->name,
            'email'    => $user->email,
            'avatar'   => $user->image,
        ];

        return [$userSocialite, $user];
    }
}
