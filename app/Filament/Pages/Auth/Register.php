<?php

namespace App\Filament\Pages\Auth;

use Coderflex\FilamentTurnstile\Forms\Components\Turnstile;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\Register as BaseRegister;

class Register extends BaseRegister
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
            Turnstile::make('captcha')
                ->theme('auto'),
        ]);
    }

    protected function throwFailureValidationException(): never
    {
        $this->dispatch('reset-captcha');

        parent::throwFailureValidationException();
    }
}
