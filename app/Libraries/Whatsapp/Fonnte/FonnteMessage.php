<?php

namespace App\Libraries\Whatsapp\Fonnte;

class FonnteMessage
{
    public function __construct(public string $target, public string $message, public int $delay) {}

    public function toArray()
    {
        return [
            'target' => $this->target,
            'message' => $this->message,
            'delay' => "{$this->delay}"
        ];
    }
}
