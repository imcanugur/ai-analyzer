<?php

declare(strict_types=1);

namespace App\DTO;

class SendNotificationDTO
{
    /**
     * Create a new DTO instance.
     *
     * @param  array<string>|null  $recipients
     */
    public function __construct(
        public readonly bool $sendToAll,
        public readonly string $title,
        public readonly string $body,
        public readonly ?string $color = 'info',
        public readonly ?string $icon = 'heroicon-o-bell',
        public readonly ?array $recipients = null
    ) {}

    /**
     * Create a DTO instance from an array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            sendToAll: (bool) ($data['send_to_all'] ?? false),
            title: $data['title'],
            body: $data['body'],
            color: $data['color'] ?? 'info',
            icon: $data['icon'] ?? 'heroicon-o-bell',
            recipients: $data['recipients'] ?? null
        );
    }
}
