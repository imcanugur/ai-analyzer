<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Role;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class RoleAssignment extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static \UnitEnum|string|null $navigationGroup = 'Filament Shield';

    protected static ?string $navigationLabel = 'Role Assignment';

    protected static ?string $title = 'Assign Roles to Users';

    protected string $view = 'filament.pages.role-assignment';

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->iconColor('primary')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Username copied!'),

                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-envelope')
                    ->iconColor('gray')
                    ->copyable()
                    ->copyMessage('Email address copied!'),

                TextColumn::make('roles.name')
                    ->label('Assigned Roles')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'panel_user' => 'gray',
                        'none' => 'warning',
                        default => 'info',
                    })
                    ->separator(', '),
            ])
            ->actions([
                Action::make('editRoles')
                    ->label('Edit Roles')
                    ->icon('heroicon-m-shield-check')
                    ->color('warning')
                    ->button()
                    ->outlined()
                    ->size('sm')
                    ->modalWidth('md')
                    ->modalHeading('Manage User Roles')
                    ->modalDescription(fn (User $record) => "Select the access roles for {$record->name} ({$record->email}).")
                    ->modalIcon('heroicon-o-shield-check')
                    ->modalIconColor('warning')
                    ->modalSubmitActionLabel('Save Roles')
                    ->form([
                        CheckboxList::make('roles')
                            ->label('Available Roles')
                            ->options(Role::pluck('name', 'name')->toArray())
                            ->columns(2)
                            ->required(),
                    ])
                    ->fillForm(fn (User $record): array => [
                        'roles' => $record->roles->pluck('name')->toArray(),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->syncRoles($data['roles']);

                        Notification::make()
                            ->title('User roles updated successfully!')
                            ->success()
                            ->send();
                    }),
            ])
            ->striped()
            ->paginated([5, 10, 25, 50])
            ->defaultPaginationPageOption(10);
    }
}
