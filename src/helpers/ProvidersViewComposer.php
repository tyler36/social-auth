<?php

namespace Tyler36\SocialAuth\Helpers;

use Illuminate\View\View;

class ProvidersViewComposer
{
    /**
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('providers', Providers::getEnabled());
    }
}
