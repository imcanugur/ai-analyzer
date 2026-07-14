<?php

use App\Providers\AIServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\Filament\AppPanelProvider;
use App\Providers\HorizonServiceProvider;

return [
    AIServiceProvider::class,
    AppServiceProvider::class,
    AppPanelProvider::class,
    HorizonServiceProvider::class,
];
