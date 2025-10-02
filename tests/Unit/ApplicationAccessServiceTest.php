<?php

use App\Services\ApplicationAccessService;
use App\Services\Authentik\AuthentikSDK;

test('service can be instantiated with authentik sdk', function () {
    $authentik = $this->createMock(AuthentikSDK::class);
    $service = new ApplicationAccessService($authentik);

    expect($service)->toBeInstanceOf(ApplicationAccessService::class);
});

// Note: Service method testing is handled in Feature tests 
// where full Laravel application context is available