<?php

namespace SocialiteProviders\ADFS;

use SocialiteProviders\Manager\SocialiteWasCalled;

class ADFSExtendSocialite
{
    /**
     * Execute the provider.
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('adfs', __NAMESPACE__.'\Provider');
    }
}
