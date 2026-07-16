@php
    $user = filament()->auth()->user();
    $profileUrl = filament()->getProfileUrl();
@endphp

<x-filament-widgets::widget class="fi-account-widget">
    @if ($profileUrl)
        <a href="{{ $profileUrl }}">
    @endif
        <x-filament::section>
            <x-filament-panels::avatar.user
                size="lg"
                :user="$user"
                loading="lazy"
            />

            <div class="fi-account-widget-main">
                <h2 class="fi-account-widget-heading">
                    {{ __('filament-panels::widgets/account-widget.welcome', ['app' => config('app.name')]) }}
                </h2>

                <p class="fi-account-widget-user-name">
                    {{ filament()->getUserName($user) }}
                </p>
            </div>

            <form
                action="{{ filament()->getLogoutUrl() }}"
                method="post"
                class="fi-account-widget-logout-form"
                onclick="event.stopPropagation();"
            >
                @csrf

                <x-filament::button
                    color="gray"
                    :icon="\Filament\Support\Icons\Heroicon::ArrowLeftEndOnRectangle"
                    :icon-alias="\Filament\View\PanelsIconAlias::WIDGETS_ACCOUNT_LOGOUT_BUTTON"
                    labeled-from="sm"
                    tag="button"
                    type="submit"
                >
                    {{ __('filament-panels::widgets/account-widget.actions.logout.label') }}
                </x-filament::button>
            </form>
        </x-filament::section>
    @if ($profileUrl)
        </a>
    @endif
</x-filament-widgets::widget>
