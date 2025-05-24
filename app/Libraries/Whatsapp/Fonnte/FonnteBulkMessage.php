<?php

namespace App\Libraries\Whatsapp\Fonnte;

class FonnteBulkMessage
{
    private array $messageWhatsapp = [];

    public function __construct(public array $messages)
    {
        foreach ($messages as $message) {
            array_push(
                $this->messageWhatsapp,
                (new FonnteMessage($message['destination'], $message['message'], $message['delay'] ?? 2))
                    ->toArray()
            );
        }
    }

    public function toArray()
    {
        return $this->messageWhatsapp;
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }
}
