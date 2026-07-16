<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Role;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleAssignment extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static \UnitEnum|string|null $navigationGroup = 'Filament Shield';

    protected static ?string $navigationLabel = 'Role Assignment';

    protected static ?string $title = 'Assign Roles to Users';

    protected string $view = 'filament.pages.role-assignment';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Select User and Assign Roles')
                    ->schema([
                        Select::make('user_id')
                            ->label('User')
                            ->placeholder('Select a user to assign roles...')
                            ->searchable()
                            ->required()
                            ->options(User::pluck('email', 'id')->toArray())
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $user = User::find($state);
                                    if ($user) {
                                        $set('roles', $user->roles->pluck('name')->toArray());
                                    }
                                } else {
                                    $set('roles', []);
                                }
                            }),

                        Select::make('roles')
                            ->label('Roles')
                            ->placeholder('Select one or more roles...')
                            ->multiple()
                            ->preload()
                            ->options(Role::pluck('name', 'name')->toArray())
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('form')
                ->color('primary')
                ->size('lg'),
        ];
    }

    public function save(): void
    {
        $formData = $this->form->getState();

        $user = User::find($formData['user_id']);
        if ($user) {
            $user->syncRoles($formData['roles']);

            Notification::make()
                ->title('User roles updated successfully!')
                ->success()
                ->send();
        }
    }
}
