<?php

namespace App\Libraries\Whatsapp;

interface Whatsapp
{
    function sendMessage(string|array $message): string;
    function getProvider(): string;
    function getToken(): string;
}
