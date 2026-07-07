<?php

use App\Providers\AIServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\Filament\AppPanelProvider;

return [
    AppServiceProvider::class,
    AppPanelProvider::class,
    AIServiceProvider::class,
];
