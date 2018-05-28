{{-- PARTIAL:   Socialauth Login --}}

<div id="login-socialauth">

    {{-- CHECK: Providers are available --}}
    @unless(empty($providers))
        <div>
            @lang('socialauth::message.login')
        </div>

        <div class="login-socialauth-buttons">
            @foreach ($providers as $provider )
                <a name="login-{{ $provider }}" class="button is-{{ $provider }}"
                   href="{{ route('login.sns', ['provider' => $provider]) }}">

                    <span class="icon"></span>
                    <span>
                        @lang('socialauth::message.'.$provider)
                    </span>
                </a>
            @endforeach
        </div>

    @else
        <div class="notification is-warning">@lang('socialauth::message.none')</div>
    @endif

</div>
