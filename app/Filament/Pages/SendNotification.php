<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Actions\SendNotificationAction;
use App\Contracts\UserRepositoryInterface;
use App\DTO\SendNotificationDTO;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class SendNotification extends Page implements HasForms
{
    use InteractsWithForms;
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationLabel = 'Send Notifications';

    protected static ?string $title = 'Send Database Notification';

    protected string $view = 'filament.pages.send-notification';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Notification Details')
                    ->schema([
                        Toggle::make('send_to_all')
                            ->label('Send to All Users')
                            ->live()
                            ->default(false),

                        Select::make('recipients')
                            ->label('Recipients')
                            ->multiple()
                            ->searchable()
                            ->options(function () {
                                $userRepository = app(UserRepositoryInterface::class);

                                return $userRepository->all()->pluck('name', 'id')->toArray();
                            })
                            ->hidden(fn (callable $get) => $get('send_to_all'))
                            ->required(fn (callable $get) => ! $get('send_to_all')),

                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('body')
                            ->label('Message Body')
                            ->required()
                            ->rows(4),

                        Select::make('color')
                            ->label('Type / Color')
                            ->options([
                                'success' => 'Success (Green)',
                                'info' => 'Info (Blue)',
                                'warning' => 'Warning (Orange)',
                                'danger' => 'Danger (Red)',
                            ])
                            ->default('info')
                            ->required(),

                        Select::make('icon')
                            ->label('Icon')
                            ->options([
                                'heroicon-o-bell' => 'Bell',
                                'heroicon-o-check-circle' => 'Check Circle',
                                'heroicon-o-exclamation-triangle' => 'Exclamation Triangle',
                                'heroicon-o-information-circle' => 'Information Circle',
                                'heroicon-o-megaphone' => 'Megaphone',
                            ])
                            ->default('heroicon-o-bell')
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('send')
                ->label('Send Notification')
                ->submit('form')
                ->color('primary')
                ->size('lg'),
        ];
    }

    public function send(SendNotificationAction $action): void
    {
        $formData = $this->form->getState();

        $action->execute(SendNotificationDTO::fromArray($formData));

        $this->form->fill();
    }
}
